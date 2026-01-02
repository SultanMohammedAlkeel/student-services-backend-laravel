<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Hall;
use App\Models\Schedules;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Student;

use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * الحصول على الجدول الدراسي للطالب الحالي
     */
    public function getStudentSchedule(Request $request)
    {
        try {
            // الحصول على بيانات الطالب المسجل دخوله
            // $student = $request->user()->student;
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // البحث عن الطالب المرتبط بالمستخدم الحالي
            $student = Student::where('id', $user->user_id)
                ->first();
            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب',
                ], 404);
            }

            // الحصول على الفترة الدراسية النشطة حالياً
            $activePeriod = Period::whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if (!$activePeriod) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا توجد فترة دراسية نشطة حالياً',
                ], 404);
            }

            $academicYearId = $activePeriod->academic_year_id;
            $termNow = $activePeriod->term;

            // الحصول على الجدول الدراسي
            $schedule = Schedules::where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->where('academic_year_id', $academicYearId)
                ->where('term', $termNow)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على جدول دراسي',
                ], 404);
            }

            // الحصول على البيانات المرتبطة
            $courses = Course::all();
            $halls = Hall::all();
            $teachers = Teacher::all();
            $departments = Department::all();

            // تنسيق البيانات للاستجابة
            $scheduleData = json_decode($schedule->schedule, true);

            // إضافة البيانات المرتبطة
            $formattedSchedule = [];
            foreach ($scheduleData as $dayIndex => $day) {
                $formattedDay = [];
                foreach ($day as $periodIndex => $period) {
                    $formattedPeriod = $period;

                    // إضافة بيانات المقرر
                    if (!empty($period['course'])) {
                        $course = $courses->where('id', $period['course'])->first();
                        if ($course) {
                            $formattedPeriod['course'] = $course->name;
                        }
                    }

                    // إضافة بيانات القاعة
                    if (!empty($period['hall'])) {
                        $hall = $halls->where('id', $period['hall'])->first();
                        if ($hall) {
                            $formattedPeriod['hall'] = $hall->name;
                            $formattedPeriod['hall_type'] = $hall->type;
                        }
                    }

                    // إضافة بيانات المدرس
                    if (!empty($period['teacher'])) {
                        $teacher = $teachers->where('id', $period['teacher'])->first();
                        if ($teacher) {
                            $formattedPeriod['teacher'] = $teacher->name;
                        }
                    }

                    $formattedDay[] = $formattedPeriod;
                }
                $formattedSchedule[] = $formattedDay;
            }

            // تجهيز البيانات للاستجابة
            $response = [
                'status' => true,
                'id' => $schedule->id,
                'department_id' => $schedule->department_id,
                'academic_year_id' => $schedule->academic_year_id,
                'level' => $schedule->level,
                'term' => $schedule->term,
                'schedule' => $formattedSchedule,
                'last_updated' => $schedule->updated_at->toIso8601String(),
                'days' => ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'],
                'periods' => ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'],
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب الجدول الدراسي',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على محاضرات اليوم للطالب الحالي
     */
    public function getTodayLectures(Request $request)
    {
        try {
            // الحصول على بيانات الطالب المسجل دخوله
            $student = auth()->guard()->user()->student;

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب',
                ], 404);
            }

            // الحصول على الفترة الدراسية النشطة حالياً
            $activePeriod = Period::whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if (!$activePeriod) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا توجد فترة دراسية نشطة حالياً',
                ], 404);
            }

            $academicYearId = $activePeriod->academic_year_id;
            $termNow = $activePeriod->term;

            // الحصول على الجدول الدراسي
            $schedule = Schedules::where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->where('academic_year_id', $academicYearId)
                ->where('term', $termNow)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على جدول دراسي',
                ], 404);
            }

            // تحديد اليوم الحالي
            $today = [
                'Saturday' => 0,
                'Sunday' => 1,
                'Monday' => 2,
                'Tuesday' => 3,
                'Wednesday' => 4,
                'Thursday' => 5,
                'Friday' => 6,
            ];

            $dayOfWeek = Carbon::now()->format('l');
            $dayIndex = $today[$dayOfWeek];

            // الجمعة ليس يوم دراسي
            if ($dayIndex == 6) {
                return response()->json([
                    'status' => true,
                    'message' => 'اليوم ليس يوم دراسي',
                    'lectures' => [],
                ]);
            }

            // الحصول على البيانات المرتبطة
            $courses = Course::all();
            $halls = Hall::all();
            $teachers = Teacher::all();

            // تنسيق البيانات للاستجابة
            $scheduleData = json_decode($schedule->schedule, true);

            // التحقق من وجود بيانات لليوم الحالي
            if (!isset($scheduleData[$dayIndex])) {
                return response()->json([
                    'status' => true,
                    'message' => 'لا توجد محاضرات اليوم',
                    'lectures' => [],
                ]);
            }

            $todaySchedule = $scheduleData[$dayIndex];
            $periods = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];

            // تجهيز محاضرات اليوم
            $lectures = [];
            foreach ($todaySchedule as $periodIndex => $period) {
                if (!empty($period['course'])) {
                    $lecture = [
                        'period' => $periods[$periodIndex],
                        'period_index' => $periodIndex,
                        'course_id' => $period['course'],
                        'hall_id' => $period['hall'],
                        'teacher_id' => $period['teacher'],
                    ];

                    // إضافة بيانات المقرر
                    $course = $courses->where('id', $period['course'])->first();
                    if ($course) {
                        $lecture['course_name'] = $course->name;
                    }

                    // إضافة بيانات القاعة
                    $hall = $halls->where('id', $period['hall'])->first();
                    if ($hall) {
                        $lecture['hall_name'] = $hall->name;
                        $lecture['hall_type'] = $hall->type;
                        $lecture['is_lab'] = strpos(strtolower($hall->type), 'معمل') !== false;
                    }

                    // إضافة بيانات المدرس
                    $teacher = $teachers->where('id', $period['teacher'])->first();
                    if ($teacher) {
                        $lecture['teacher_name'] = $teacher->name;
                    }

                    $lectures[] = $lecture;
                }
            }

            return response()->json([
                'status' => true,
                'day' => $dayOfWeek,
                'day_name' => ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'][$dayIndex],
                'lectures' => $lectures,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب محاضرات اليوم',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على محاضرات الغد للطالب الحالي
     */
    public function getTomorrowLectures(Request $request)
    {
        try {
            // الحصول على بيانات الطالب المسجل دخوله
            $student = auth()->guard()->user()->student;

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على بيانات الطالب',
                ], 404);
            }

            // الحصول على الفترة الدراسية النشطة حالياً
            $activePeriod = Period::whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if (!$activePeriod) {
                return response()->json([
                    'status' => false,
                    'message' => 'لا توجد فترة دراسية نشطة حالياً',
                ], 404);
            }

            $academicYearId = $activePeriod->academic_year_id;
            $termNow = $activePeriod->term;

            // الحصول على الجدول الدراسي
            $schedule = Schedules::where('department_id', $student->department_id)
                ->where('level', $student->level)
                ->where('academic_year_id', $academicYearId)
                ->where('term', $termNow)
                ->first();

            if (!$schedule) {
                return response()->json([
                    'status' => false,
                    'message' => 'لم يتم العثور على جدول دراسي',
                ], 404);
            }

            // تحديد يوم الغد
            $today = [
                'Saturday' => 0,
                'Sunday' => 1,
                'Monday' => 2,
                'Tuesday' => 3,
                'Wednesday' => 4,
                'Thursday' => 5,
                'Friday' => 6,
            ];

            $dayOfWeek = Carbon::now()->format('l');
            $todayIndex = $today[$dayOfWeek];

            // حساب فهرس يوم الغد
            $tomorrowIndex = ($todayIndex + 1) % 7;

            // الجمعة ليس يوم دراسي
            if ($tomorrowIndex == 6) {
                return response()->json([
                    'status' => true,
                    'message' => 'الغد ليس يوم دراسي',
                    'lectures' => [],
                ]);
            }

            // الحصول على البيانات المرتبطة
            $courses = Course::all();
            $halls = Hall::all();
            $teachers = Teacher::all();

            // تنسيق البيانات للاستجابة
            $scheduleData = json_decode($schedule->schedule, true);

            // التحقق من وجود بيانات ليوم الغد
            if (!isset($scheduleData[$tomorrowIndex])) {
                return response()->json([
                    'status' => true,
                    'message' => 'لا توجد محاضرات غداً',
                    'lectures' => [],
                ]);
            }

            $tomorrowSchedule = $scheduleData[$tomorrowIndex];
            $periods = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];
            $days = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'];

            // تجهيز محاضرات الغد
            $lectures = [];
            foreach ($tomorrowSchedule as $periodIndex => $period) {
                if (!empty($period['course'])) {
                    $lecture = [
                        'period' => $periods[$periodIndex],
                        'period_index' => $periodIndex,
                        'course_id' => $period['course'],
                        'hall_id' => $period['hall'],
                        'teacher_id' => $period['teacher'],
                    ];

                    // إضافة بيانات المقرر
                    $course = $courses->where('id', $period['course'])->first();
                    if ($course) {
                        $lecture['course_name'] = $course->name;
                    }

                    // إضافة بيانات القاعة
                    $hall = $halls->where('id', $period['hall'])->first();
                    if ($hall) {
                        $lecture['hall_name'] = $hall->name;
                        $lecture['hall_type'] = $hall->type;
                        $lecture['is_lab'] = strpos(strtolower($hall->type), 'معمل') !== false;
                    }

                    // إضافة بيانات المدرس
                    $teacher = $teachers->where('id', $period['teacher'])->first();
                    if ($teacher) {
                        $lecture['teacher_name'] = $teacher->name;
                    }

                    $lectures[] = $lecture;
                }
            }

            return response()->json([
                'status' => true,
                'day' => array_keys($today)[$tomorrowIndex],
                'day_name' => $days[$tomorrowIndex],
                'lectures' => $lectures,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء جلب محاضرات الغد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
