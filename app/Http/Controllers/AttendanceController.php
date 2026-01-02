<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Hall;
use App\Models\HallBooking;
use App\Models\Notification;
use App\Models\NotificationReply;
use App\Models\Period;
use App\Models\Schedules;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }

        $attendance = Attendance::all();
        return view('students.pages.attendances', [
            'attendance' => $attendance,
            'periods' => ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'],
            'courses' => Course::all(),
            'teachers' => Teacher::all(),
            'academic_years' => AcademicYear::all(),
            'halls' => Hall::all(),
        ]);
    }

    function GetStudents() 
    {
        $students = Student::where('department_id', session('department_id'))
                    ->where('level', session('level'))
                    ->select('id', 'name')
                    ->get();
        
        return response()->json($students);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $period = [
            '08:00 - 10:00' => 0, 
            '10:00 - 12:00' => 1, 
            '12:00 - 02:00' => 2,
        ];
        $table = $this->GetStudentTomorrowSchedule()[$period[$request->period]];

        $attendance = new Attendance();
        $attendance->period = $request->period;
        $attendance->course_id = $table['course'];
        $attendance->teacher_id = $table['teacher'];
        $attendance->academic_year_id = $activePeriods->first()->academic_year_id;
        $attendance->level = session('level');
        $attendance->department_id = session('department_id');
        $attendance->lecture_date = Carbon::now()->format('Y-m-d');
        $attendance->lecture_number = $request->lecture_number;
        $attendance->data = $request->data;
        $attendance->save();

        return view('students.pages.attendances-record', [
            'data' => json_decode($request->data, true),
            'period' => $request->period,
            'course' => Course::find($table['course'])->name,
            'teacher' => Teacher::find($table['teacher'])->name,
        ]);
    
    }

    public function GetStudentsAttendancesRecord(Request $request) 
    {
        $attendance = Attendance::where('teacher_id', session('user_ref_id'))
        ->where('department_id', $request->department_id)
        ->where('level', $request->level)
        ->get();
        if ($attendance->count() != 0) {
            $course = Course::find($attendance[0]->course_id)->name;
            $level = $attendance[0]->level;
        } else {
            $course = '';
            $level = '';
        }

        return view('teacher.pages.attendances', [
            'attendances' => $attendance,
            'courses' => $course,
            'level' => $level,
        ]);
    }

    public function GetMyAttendanceRecord() 
    {
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $myID = session('user_ref_id');

        
        // البحث عن جميع سجلات الحضور التي تحتوي على هذا الطالب
        $attendances = Attendance::whereRaw('JSON_CONTAINS(data, ?)', [json_encode(['id' => $myID])])
        ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->get();
            
        $results = $attendances->map(function ($attendance) use ($myID) {
            $students = json_decode($attendance->data, true);
            return [
                'attendance_id' => $attendance->id,
                'lecture_date' => $attendance->lecture_date,
                'course_id' => $attendance->course_id,
                'teacher_id' => $attendance->teacher_id,
                'level' => $attendance->level,
                'name' => collect($students)->firstWhere('id', $myID)['name'],
                'status' => collect($students)->firstWhere('id', $myID)['status']
            ];
        });

        $activePeriods = Period::whereDate('start_date', '<=', now())
                 ->whereDate('end_date', '>=', now())
                 ->get();

    $results = DB::table('schedules')
    ->where('term', $activePeriods->first()->term)
    ->where('academic_year_id', $activePeriods->first()->academic_year_id)
    ->where('department_id', session('department_id'))
    ->where('level', session('level'))
    ->select('id', 'department_id', 'level', 'term', 'schedule')
    ->get()
    ->map(function ($item) {
        $scheduleData = json_decode($item->schedule, true);
        $filteredSchedule = [];
        foreach ($scheduleData as $dayIndex => $day) {
            foreach ($day as $periodIndex => $period) {
                if ($period['teacher'] != '') {
                    $filteredSchedule[] = [
                        'course' => $period['course'],
                        'teacher' => $period['teacher']
                    ];
                }
            }
        }
        
        return $filteredSchedule;
    })
    ->collapse()
    ->unique(function ($item) {
        return $item['course'].$item['teacher'];
    });
        
        return $results;
    }

    function CheckLecture(Request $request) 
    {
        $period = [
            '08:00 - 10:00' => 0, 
            '10:00 - 12:00' => 1, 
            '12:00 - 02:00' => 2,
        ];
        $table = $this->GetStudentTomorrowSchedule()[$period[$request->period]];
        
        $attendance = Attendance::where('period', $request->period)
        ->where('department_id', session('department_id'))
        ->where('level', session('level'))
        ->where('lecture_date', Carbon::now()->format('Y-m-d'))
        ->get();
        
        if (!$table['course']) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد محاضرات في هذا الفترة الرجاء التاكد من الفترة'
            ]);
        }
        
        if ($attendance->count() != 0) {
            return response()->json([
                'success' => false,
                'message' => 'تم اخذ الحضور مسبقا'
            ]);
        }
        return response()->json([
            'success' => true,
        ]);
    }

    function GetStudentTomorrowSchedule() 
    {
        $today = [
            'Saturday' => '0',
            'Sunday' => '1',
            'Monday' => '2',
            'Tuesday' => '3',
            'Wednesday' => '4',
            'Thursday' => '5',
            'Friday' => '6',
        ];

        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();


        
        $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
        $_period = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];

        $termNow = @$activePeriods->first()->term;
        $schedule = DB::table('schedules')
            ->where('department_id', session('department_id'))
            ->where('level', session('level'))
            ->where('term', $termNow)
            ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->select('id', 'department_id', 'level', 'term', 'schedule')
            ->get()
            ->map(function ($item) use ($today, $_period)  {
                $scheduleData = json_decode($item->schedule, true);
                $filteredSchedule = [];
                foreach ($scheduleData as $dayIndex => $day) {
                    foreach ($day as $periodIndex => $period) {
                        if (($today[date('l')] == 6 ? 0 : $today[date('l')] +1) == $dayIndex) {
                            $filteredSchedule[] = [
                                'id' => $item->id,
                                'day' => $dayIndex,
                                'period' => $_period[$periodIndex],
                                'course' => $period['course'],
                                'teacher' => $period['teacher'],
                                'hall' => $period['hall'],
                                'department_id' => $item->department_id,
                                'level' => $item->level,
                                'term' => $item->term
                            ];
                        }
                    }
                }
                return $filteredSchedule;
            })
            ->collapse();
            return $schedule;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
