<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * الحصول على سجلات الحضور للطالب المسجل دخوله
     * 
     * @return JsonResponse
     */
    public function getStudentAttendanceRecords(): JsonResponse
    {
        try {
            $user = Auth::user();
                        if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            $student = Student::where('id', $user->user_id)
                ->first();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب'
                ], 404);
            }

            // الحصول على الفترة الأكاديمية النشطة
            $activePeriods = Period::whereDate('start_date', '<=', now())
                         ->whereDate('end_date', '>=', now())
                         ->get();

            if ($activePeriods->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد فترة أكاديمية نشطة حالياً'
                ], 404);
            }

            $activePeriod = $activePeriods->first();

            // البحث عن جميع سجلات الحضور التي تحتوي على هذا الطالب
            $attendances = Attendance::whereRaw('JSON_CONTAINS(data, ?)', [json_encode(['id' => $student->id])])
                ->where('academic_year_id', $activePeriod->academic_year_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->orderBy('lecture_date', 'desc')
                ->get();

            // تجميع البيانات حسب المقرر
            $courseAttendances = [];
            
            foreach ($attendances as $attendance) {
                $students = json_decode($attendance->data, true);
                $studentRecord = collect($students)->firstWhere('id', $student->id);
                
                if ($studentRecord) {
                    $courseId = $attendance->course_id;
                    
                    if (!isset($courseAttendances[$courseId])) {
                        $course = Course::find($courseId);
                        $teacher = Teacher::find($attendance->teacher_id);
                        
                        $courseAttendances[$courseId] = [
                            'course_id' => $courseId,
                            'course_name' => $course ? $course->name : 'غير معروف',
                            'course_code' => $course ? $course->code : 'غير معروف',
                            'teacher_name' => $teacher ? $teacher->name : 'غير معروف',
                            'total_lectures' => 0,
                            'attended_lectures' => 0,
                            'absent_lectures' => 0,
                            'attendance_percentage' => 0,
                            'lectures' => []
                        ];
                    }
                    
                    $courseAttendances[$courseId]['total_lectures']++;
                    
                    if ($studentRecord['status'] == '1') {
                        $courseAttendances[$courseId]['attended_lectures']++;
                    } else {
                        $courseAttendances[$courseId]['absent_lectures']++;
                    }
                    
                    $courseAttendances[$courseId]['lectures'][] = [
                        'attendance_id' => $attendance->id,
                        'lecture_date' => $attendance->lecture_date,
                        'lecture_number' => $attendance->lecture_number,
                        'period' => $attendance->period,
                        'status' => $studentRecord['status'] == '1' ? 'حاضر' : 'غائب',
                        'status_code' => $studentRecord['status']
                    ];
                }
            }

            // حساب نسبة الحضور لكل مقرر
            foreach ($courseAttendances as &$courseData) {
                if ($courseData['total_lectures'] > 0) {
                    $courseData['attendance_percentage'] = round(
                        ($courseData['attended_lectures'] / $courseData['total_lectures']) * 100, 
                        2
                    );
                }
                
                // ترتيب المحاضرات حسب التاريخ (الأحدث أولاً)
                usort($courseData['lectures'], function($a, $b) {
                    return strtotime($b['lecture_date']) - strtotime($a['lecture_date']);
                });
            }

            // تحويل إلى مصفوفة مفهرسة
            $result = array_values($courseAttendances);

            return response()->json([
                'success' => true,
                'message' => 'تم جلب سجلات الحضور بنجاح',
                'data' => [
                    'student_info' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'card' => $student->card,
                        'level' => $student->level,
                        'department_id' => $student->department_id
                    ],
                    'courses_attendance' => $result,
                    'summary' => [
                        'total_courses' => count($result),
                        'total_lectures' => array_sum(array_column($result, 'total_lectures')),
                        'total_attended' => array_sum(array_column($result, 'attended_lectures')),
                        'total_absent' => array_sum(array_column($result, 'absent_lectures')),
                        'overall_percentage' => count($result) > 0 ? 
                            round(array_sum(array_column($result, 'attendance_percentage')) / count($result), 2) : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب سجلات الحضور',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على تفاصيل حضور مقرر معين
     * 
     * @param int $courseId
     * @return JsonResponse
     */
    public function getCourseAttendanceDetails(int $courseId): JsonResponse
    {
        try {
            $user = Auth::user();
                        if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            $student = Student::where('id', $user->user_id)
                ->first();
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب'
                ], 404);
            }

            // التحقق من وجود المقرر
            $course = Course::find($courseId);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على المقرر المطلوب'
                ], 404);
            }

            // الحصول على الفترة الأكاديمية النشطة
            $activePeriods = Period::whereDate('start_date', '<=', now())
                         ->whereDate('end_date', '>=', now())
                         ->get();

            if ($activePeriods->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد فترة أكاديمية نشطة حالياً'
                ], 404);
            }

            $activePeriod = $activePeriods->first();

            // البحث عن سجلات الحضور لهذا المقرر
            $attendances = Attendance::where('course_id', $courseId)
                ->whereRaw('JSON_CONTAINS(data, ?)', [json_encode(['id' => $student->id])])
                ->where('academic_year_id', $activePeriod->academic_year_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->orderBy('lecture_date', 'desc')
                ->get();

            $lectures = [];
            $totalLectures = 0;
            $attendedLectures = 0;

            foreach ($attendances as $attendance) {
                $students = json_decode($attendance->data, true);
                $studentRecord = collect($students)->firstWhere('id', $student->id);
                
                if ($studentRecord) {
                    $totalLectures++;
                    $isAttended = $studentRecord['status'] == '1';
                    
                    if ($isAttended) {
                        $attendedLectures++;
                    }
                    
                    $lectures[] = [
                        'attendance_id' => $attendance->id,
                        'lecture_date' => $attendance->lecture_date,
                        'lecture_number' => $attendance->lecture_number,
                        'period' => $attendance->period,
                        'status' => $isAttended ? 'حاضر' : 'غائب',
                        'status_code' => $studentRecord['status'],
                        'teacher_name' => Teacher::find($attendance->teacher_id)?->name ?? 'غير معروف'
                    ];
                }
            }

            $attendancePercentage = $totalLectures > 0 ? 
                round(($attendedLectures / $totalLectures) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'تم جلب تفاصيل حضور المقرر بنجاح',
                'data' => [
                    'course_info' => [
                        'id' => $course->id,
                        'name' => $course->name,
                        'code' => $course->code,
                        'level' => $course->level
                    ],
                    'attendance_summary' => [
                        'total_lectures' => $totalLectures,
                        'attended_lectures' => $attendedLectures,
                        'absent_lectures' => $totalLectures - $attendedLectures,
                        'attendance_percentage' => $attendancePercentage
                    ],
                    'lectures' => $lectures
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل حضور المقرر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات الحضور العامة للطالب
     * 
     * @return JsonResponse
     */
    public function getAttendanceStatistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $student = Student::where('email', $user->email)->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب'
                ], 404);
            }

            // الحصول على الفترة الأكاديمية النشطة
            $activePeriods = Period::whereDate('start_date', '<=', now())
                         ->whereDate('end_date', '>=', now())
                         ->get();

            if ($activePeriods->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد فترة أكاديمية نشطة حالياً'
                ], 404);
            }

            $activePeriod = $activePeriods->first();

            // الحصول على المقررات التي يدرسها الطالب من الجدول الدراسي
            $studentCourses = $this->getStudentCoursesFromSchedule($student, $activePeriod);

            // البحث عن جميع سجلات الحضور للطالب
            $attendances = Attendance::whereRaw('JSON_CONTAINS(data, ?)', [json_encode(['id' => $student->id])])
                ->where('academic_year_id', $activePeriod->academic_year_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->get();

            $totalLectures = 0;
            $attendedLectures = 0;
            $coursesWithAttendance = [];

            foreach ($attendances as $attendance) {
                $students = json_decode($attendance->data, true);
                $studentRecord = collect($students)->firstWhere('id', $student->id);
                
                if ($studentRecord) {
                    $totalLectures++;
                    if ($studentRecord['status'] == '1') {
                        $attendedLectures++;
                    }
                    
                    $courseId = $attendance->course_id;
                    if (!in_array($courseId, $coursesWithAttendance)) {
                        $coursesWithAttendance[] = $courseId;
                    }
                }
            }

            $overallPercentage = $totalLectures > 0 ? 
                round(($attendedLectures / $totalLectures) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'تم جلب إحصائيات الحضور بنجاح',
                'data' => [
                    'overall_statistics' => [
                        'total_courses' => count($studentCourses),
                        'courses_with_attendance' => count($coursesWithAttendance),
                        'total_lectures' => $totalLectures,
                        'attended_lectures' => $attendedLectures,
                        'absent_lectures' => $totalLectures - $attendedLectures,
                        'attendance_percentage' => $overallPercentage
                    ],
                    'period_info' => [
                        'term' => $activePeriod->term,
                        'start_date' => $activePeriod->start_date,
                        'end_date' => $activePeriod->end_date,
                        'academic_year_id' => $activePeriod->academic_year_id
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات الحضور',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على مقررات الطالب من الجدول الدراسي
     * 
     * @param Student $student
     * @param Period $activePeriod
     * @return array
     */
    private function getStudentCoursesFromSchedule(Student $student, Period $activePeriod): array
    {
        try {
            $schedules = DB::table('schedules')
                ->where('term', $activePeriod->term)
                ->where('academic_year_id', $activePeriod->academic_year_id)
                ->where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->select('schedule')
                ->get();

            $courses = [];

            foreach ($schedules as $schedule) {
                $scheduleData = json_decode($schedule->schedule, true);
                
                if (is_array($scheduleData)) {
                    foreach ($scheduleData as $day) {
                        if (is_array($day)) {
                            foreach ($day as $period) {
                                if (is_array($period)) {
                                    foreach ($period as $cell) {
                                        if (isset($cell['course']) && !empty($cell['course']) && !in_array($cell['course'], $courses)) {
                                            $courses[] = $cell['course'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $courses;

        } catch (\Exception $e) {
            return [];
        }
    }
}

