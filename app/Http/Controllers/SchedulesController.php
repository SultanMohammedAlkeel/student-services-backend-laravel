<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Building;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Hall;
use App\Models\HallBooking;
use App\Models\Notification;
use App\Models\NotificationReply;
use App\Models\Period;
use App\Models\Schedules;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $academic_year = $activePeriods->first()->academic_year_id;
        $termNow = $activePeriods->first()->term;

        $schedules = Schedules::where('term', $termNow)->where('academic_year_id', $academic_year)->get();
        $colleges = College::all();
        $departments = Department::all();
        $buildings = Building::all();
        $halls = Hall::all();
        $teachers = Teacher::all();
        $academic_years = AcademicYear::orderBy('id', 'desc')->get();
        
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['متطلب','مقرر','عام'];    
        $terms = ['الاول','الثاني'];
        

        return view('admins.setup.schedule', [
            'schedules' => $schedules,
            'colleges' => $colleges,
            'departments' => $departments,
            'buildings' => $buildings,
            'halls' => $halls,
            'teachers' => $teachers,
            'academic_years' => $academic_years,
            'levels' => $levels,
            'types' => $types,
            'terms' => $terms,
        ]);
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
        $schedules = new Schedules();
        $schedules->department_id = $request->department_id;
        $schedules->academic_year_id = $request->academic_year;
        $schedules->level = $request->level;
        $schedules->term = $request->term;
        $schedules->schedule = $request->schedule;
        $schedules->save();
        return redirect('/schedule');
    }
    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $courses = DB::table('courses')
        ->join('departments', 'courses.department_id', '=', 'departments.id')
        ->select('courses.id', 'courses.name as course_name', 'departments.name as department_name', 'level', 'type')
        ->where('courses.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($courses);
    }

    public function getDepartments($college_id)
    {
        // جلب الأقسام التابعة للكلية المحددة
        $departments = Department::where('college_id', $college_id)->get();
        return response()->json($departments);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
        
        $schedules = Schedules::all()->where('id', $id);
        $departments = Department::all();
        $halls = Hall::all();
        $teachers = Teacher::all();
        $courses = Course::all();
        $schedule = json_decode($schedules->first()->schedule, true);

        return view('schedule.get-schedule', [
            'schedules' => $schedules,
            'departments' => $departments,
            'halls' => $halls,
            'teachers' => $teachers,
            'days' => $days,
            'schedule' => $schedule,
            'courses' => $courses
        ]);
    }
    public function GetStudentSchedule()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
        
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        if ($activePeriods->first() == null) {
            
            return view('schedule.tomorrow', [
                'days' => $days,
                'schedule' => null,
                'departments' => null,
                'halls' => null,
                'teachers' => null,
                'courses' => null,
                'period' => null,
                'message' => 'لا يوجد لديك جدول محاضرات',   
                'activePeriods' => false
            ]);
        }
        $academic_year = $activePeriods->first()->academic_year_id;
        $termNow = $activePeriods->first()->term;
        $schedules = Schedules::all()
        ->where('department_id', session('department_id'))
        ->where('level', session('level'))
        ->where('academic_year_id', $academic_year)
        ->where('term', $termNow);
        if ($schedules->count() == 0) {
            return 'لا يوجد ';
        }
        $departments = Department::all();
        $halls = Hall::all();
        $teachers = Teacher::all();
        $courses = Course::all();
        $schedule = json_decode($schedules->first()->schedule, true);

        return view('schedule.get-schedule', [
            'schedules' => $schedules,
            'departments' => $departments,
            'halls' => $halls,
            'teachers' => $teachers,
            'days' => $days,
            'schedule' => $schedule,
            'courses' => $courses
        ]);
    }
    


    public function GetAllTeacher()  {
        $teachers = DB::table('schedules')
        ->selectRaw('DISTINCT JSON_EXTRACT(schedule, "$[*][*].teacher") as teachers')
        ->get()
        ->flatMap(function ($item) {
            return array_filter(json_decode($item->teachers), function($teacher) {
                return $teacher !== "";
            });
        })
        ->unique();
    }

    public function GetTeacherSchedule() {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $teacherId = session('user_ref_id');
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $termNow = $activePeriods->first()->term;
        $schedule = DB::table('schedules')
            ->where('term', $termNow)
            ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->select('id', 'department_id', 'level', 'term', 'schedule')
            ->get()
            ->map(function ($item) use ($teacherId, $termNow) {
                $scheduleData = json_decode($item->schedule, true);
                $filteredSchedule = [];
                foreach ($scheduleData as $dayIndex => $day) {
                    foreach ($day as $periodIndex => $period) {
                        if ($period['teacher'] == $teacherId) {
                            $filteredSchedule[] = [
                                'id' => $item->id,
                                'day' => $dayIndex,
                                'period' => $periodIndex,
                                'course' => $period['course'],
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

            $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
            $period = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];
        
            
        
            $departments = Department::all();
            $halls = Hall::all();
            $teachers = Teacher::all();
            $courses = Course::all();

            // return $schedule;
            return view('schedule.schedule', [
                'schedule' => $schedule,
                'departments' => $departments,
                'halls' => $halls,
                'teachers' => $teachers,
                'days' => $days,
                'courses' => $courses,
                'period' => $period,
            ]);
    }

    public function GetTomorrowSchedule() {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $today = [
            'Saturday' => '0',
            'Sunday' => '1',
            'Monday' => '2',
            'Tuesday' => '3',
            'Wednesday' => '4',
            'Thursday' => '5',
            'Friday' => '6',
        ];
        $teacherId = session('user_ref_id');
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $termNow = $activePeriods->first()->term;

        if ($activePeriods->first() == null) {
            
            return view('schedule.tomorrow', [
                'schedule' => null,
                'departments' => null,
                'halls' => null,
                'teachers' => null,
                'days' => null,
                'courses' => null,
                'period' => null,
                'message' => 'لا يوجد لديك محاضرات غدا',
                'activePeriods' => false
            ]);
        }
        $schedule = DB::table('schedules')
            ->where('term', $termNow)
            ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->select('id', 'department_id', 'level', 'term', 'schedule')
            ->get()
            ->map(function ($item) use ($teacherId, $today) {
                $scheduleData = json_decode($item->schedule, true);
                $filteredSchedule = [];
                foreach ($scheduleData as $dayIndex => $day) {
                    foreach ($day as $periodIndex => $period) {
                        if ($period['teacher'] == $teacherId && ($today[date('l')] == 6 ? 0 : $today[date('l')]+1) == $dayIndex) {
                            $filteredSchedule[] = [
                                'id' => $item->id,
                                'day' => $dayIndex,
                                'period' => $periodIndex,
                                'course' => $period['course'],
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

            $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
            $period = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];
        
            
        
            $departments = Department::all();
            $halls = Hall::all();
            $teachers = Teacher::all();
            $courses = Course::all();
          

            // احسب بداية ونهاية الأسبوع الحالي
            $startOfWeek = Carbon::now()->startOfWeek(); // الاثنين (أو الأحد حسب الإعدادات)
            $endOfWeek = Carbon::now()->endOfWeek();     // الأحد (أو السبت حسب الإعدادات)

            $notification = Notification::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                                        ->get();
            // return $schedule;

            $notification_reply = NotificationReply::all();

            return view('schedule.tomorrow', [
                'schedule' => $schedule,
                'departments' => $departments,
                'halls' => $halls,
                'teachers' => $teachers,
                'days' => $days,
                'courses' => $courses,
                'period' => $period,
                'activePeriods' => true,
                'notification' => $notification,
                'notification_reply' => $notification_reply
            ]);
    }
    public function GetStudentTomorrowSchedule() {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
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

        if ($activePeriods->first() == null) {
            
            return view('schedule.tomorrow', [
                'schedule' => null,
                'departments' => null,
                'halls' => null,
                'teachers' => null,
                'days' => null,
                'courses' => null,
                'period' => null,
                'message' => 'لا يوجد لديك محاضرات غدا',
                'activePeriods' => false
            ]);
        }
        $termNow = @$activePeriods->first()->term;
        $schedule = DB::table('schedules')
            ->where('department_id', session('department_id'))
            ->where('level', session('level'))
            ->where('term', $termNow)
            ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->select('id', 'department_id', 'level', 'term', 'schedule')
            ->get()
            ->map(function ($item) use ($today) {
                $scheduleData = json_decode($item->schedule, true);
                $filteredSchedule = [];
                foreach ($scheduleData as $dayIndex => $day) {
                    foreach ($day as $periodIndex => $period) {
                        if (($today[date('l')] == 6 ? 0 : $today[date('l')]+1) == $dayIndex) {
                            $filteredSchedule[] = [
                                'id' => $item->id,
                                'day' => $dayIndex,
                                'period' => $periodIndex,
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
            
            $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
            $period = ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00'];
        
            
        
            $departments = Department::all();
            $halls = Hall::all();
            $teachers = Teacher::all();
            $courses = Course::all();
          
            
            // احسب بداية ونهاية الأسبوع الحالي
            $startOfWeek = Carbon::now()->startOfWeek(); // الاثنين (أو الأحد حسب الإعدادات)
            $endOfWeek = Carbon::now()->endOfWeek();     // الأحد (أو السبت حسب الإعدادات)
            
            $notification = Notification::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('schedule_id', @$schedule[0]['id'])
            ->get();
            $hall_booking = HallBooking::where('schedule_id', @$schedule[0]['id'])->get();
            $notification_reply = NotificationReply::all();

            return view('schedule.tomorrow', [
                'schedule' => $schedule,
                'departments' => $departments,
                'halls' => $halls,
                'teachers' => $teachers,
                'days' => $days,
                'courses' => $courses,
                'period' => $period,
                'activePeriods' => true,
                'hall_booking' => $hall_booking,
                'notification' => $notification,
                'notification_reply' => $notification_reply
            ]);
    }

    public function GetLevelsSchedule() {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
    
        $teacherId = session('user_ref_id');
        
        // الحصول على الفترة النشطة
        $activePeriods = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        
        if ($activePeriods->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد فترات نشطة حالياً');
        }
    
        $termNow = $activePeriods->first()->term;
        $academicYearId = $activePeriods->first()->academic_year_id;
    
        // الحصول على الجدول
        $scheduleItems = DB::table('schedules')
            ->where('term', $termNow)
            ->where('academic_year_id', $academicYearId)
            ->select('id', 'department_id', 'level', 'term', 'schedule')
            ->get();
    
        $uniqueLevels = [];
    
        foreach ($scheduleItems as $item) {
            $scheduleData = json_decode($item->schedule, true);
            
            foreach ($scheduleData as $day) {
                foreach ($day as $period) {
                    if ($period['teacher'] == $teacherId) {
                        // إضافة المستوى إذا لم يكن موجوداً
                        if (!in_array($item->level, $uniqueLevels) && !in_array($item->department_id, $uniqueLevels)) {
                            $uniqueLevels[] = [
                                'department_id' => $item->department_id,
                                'level' => $item->level
                            ];
                        }
                        break; // الخروج من حلقة الفترات بعد العثور على المدرس
                    }
                }
            }
        }

        // return ;
        return view('teacher.pages.levels', [
            'levels' => $this->removeDuplicates($uniqueLevels),
            'departments' => Department::all()
        ]);
    }

    function removeDuplicates($array) {
        $serialized = array_map(function($item) {
            return json_encode($item);
        }, $array);
    
        $unique = array_unique($serialized);
        
        return array_map(function($item) {
            return json_decode($item, true);
        }, $unique);
    }

    public function GetCourceCount() {
        $teacherStats = DB::table('schedules')
        ->select('schedule')
        ->get()
        ->flatMap(function ($item) {
            return collect(json_decode($item->schedule, true))
                ->flatMap(function ($day) {
                    return collect($day)->pluck('teacher');
                });
        })
        ->filter()
        ->countBy()
        ->sortDesc();

        return $teacherStats;
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
