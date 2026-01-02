<?php
use App\Http\Controllers\Academic_yearController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\userController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\College_BuildingsController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentsDataController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UniversityController;
use App\Models\Academic_year;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Building;
use App\Models\College;
use App\Models\ContactInfo;
use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Department;
use App\Models\Hall;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

session_start();

Route::get('/', function () {
    $user = new User();
    $university = new University();
    $colleges = new College();
    $departments = new Department();
    $buildings = new Building();
    $halls = new Hall();
    $teachers = new Teacher();
    $students = new Student();

    if ($university->count() ==  0) {
        return view('setup.index');
    }

    if ($user->count() == 0) {
        return view('setup.pages.account');
    }

    if (session('logged_in')) {

        if (session('mode') == 'admin') {
            return view('admins.index');
        }

        if (session('user_type') == 'مشرف') {
            return view('index', [
                'colleges' => $colleges,
                'departments' => $departments,
                'buildings' => $buildings,
                'halls' => $halls,
                'teachers' => $teachers,
                'students' => $students,
            ]);
        } else if (session('user_type') == 'معلم') {

            return view('teacher.index', [
                'colleges' => $colleges,
                'departments' => $departments,
                'buildings' => $buildings,
                'halls' => $halls,
                'teachers' => $teachers,
                'students' => $students,
                'notification' => Notification::where('teacher_id', session('user_ref_id'))->where('is_delivered', 0)->get()
            ]);
        } else {
            return view('students.index', [
            'colleges' => $colleges,
            'departments' => $departments,
            'buildings' => $buildings,
            'halls' => $halls,
            'teachers' => $teachers,
            'students' => $students,
        ]);
        }

    } else {
        return redirect('/welcome');
    }
});

Route::get('/welcome', function () {
    return view(view: 'welcome.index');
});


Route::get('/welcome-admin', function () {
    session(['user_type' => 'مشرف']);
    return view(view: 'welcome.welcome');
});


Route::get('/student-sign-in', function () {
    session(['user_type' => 'طالب']);
    return view('auth.sign-in');
});

Route::get('/teacher-sign-in', function () {
    session(['user_type' => 'معلم']);
    return view('auth.sign-in');
});

Route::get('/create-account-teacher', function () {
    session(['user_type' => 'معلم']);
    return view('auth.add-account');
});

Route::get('/create-account-student', function () {
    session(['user_type' => 'طالب']);
    return view('auth.add-account');
});

Route::post('/sign', function (Request $request) {
    // البحث عن المستخدم باستخدام اسم المستخدم
    $user = User::where('name', $request->name)->where('user', session('user_type'))->first();
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'password' => 'required',
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $academic_ranks = [
        'أستاذ دكتور' => 'أ.د.',
        'أستاذ مشارك' => 'أ.م.',
        'أستاذ مساعد' => 'أ.م.', 
        'مدرس' => 'د.', 
        'معيد' => 'م.'
    ];

    // التحقق من وجود المستخدم وصحة كلمة المرور
    if ($user) {
        if (!$user->is_active) {
            return back()->withErrors(['user' => 'هذا الحساب موقف من قبل المشرفين']);
        }
        if (Hash::check($request->password, $user->password)) {
            session(['user_id' => $user->id]);
            session(['username' => $user->name]);
            session(['email' => $user->email]);
            session(['image_url' => $user->image_url]);
            session(['role_id' => $user->role_id]);
            session(['phone_number' => $user->phone_number]);
            session(['user_type' => $user->user]);
            session(['user_ref_id' => $user->user_id]);
            session(['logged_in' => true]);
    
            $user->last_login = now();
            $user->status = 'متصل';
            $user->save();

            if (session('user_type') == 'معلم') {
                $teacher = Teacher::find(session('user_ref_id'));
                $getRank = $teacher->academic_degree;
                $name = $academic_ranks[$getRank] .' '. $user->name;
                session(['username' => $name]);
                session(['college' => $teacher->college_id]);
            }
            if (session('user_type') == 'طالب') {
                $student = Student::find(session('user_ref_id'));
                $college_id = Department::find($student->department_id)->college_id;
                $college = College::find($college_id)->id;
                session(['sname' => $student->name]);
                session(['card' => $student->card]);
                session(['gender' => $student->gender]);
                session(['birth_date' => $student->birth_date]);
                session(['address' => $student->address]);
                session(['college' => $college]);
                session(['department_id' => $student->department_id]);
                session(['level' => $student->level]);
                session(['login' => $student->updated_at]);
            }
            return redirect('/'); // توجيه المستخدم إلى الصفحة الرئيسية
        }
        // حفظ بيانات المستخدم في الجلسة
        return back()->withErrors(['password' => ' كلمة المرور غير صحيحة']);
    }
    return back()->withErrors(['user' => 'اسم المستخدم  غير صحيح']);
});


Route::get('/sign-out', function () {
    $user = User::find(session('user_id'));
    $user->status = 'غير متصل';
    $user->last_logout = now();
    $user->save();
    session()->flush(); // حذف جميع البيانات المخزنة في الجلسة
    return redirect('/'); // توجيه المستخدم إلى الصفحة الرئيسية
});

Route::get('/setup', function () {
    $university = new University();
    $user = new User();

    if ($user->count() == 0) {
        return view('setup.pages.account');
    }
    
    if ($university->count() == 0) {
        return view('setup.pages.university');
    }
    return redirect('/');
});


Route::get('/setup-t', function () {
    $university = new University();
    $user = new User();

    if ($user->count() == 0) {
        return view('setup.pages.account');
    }
    
    if ($university->count() == 0) {
        return view('setup.pages.university');
    }
    return view('setup.index');
});


Route::get('/admin-mode', function () {
    session(['mode' => 'admin']);
    return redirect('/');
});


Route::get('/user-mode', function () {
    session(['mode' => 'user']);
    return redirect('/');
});


Route::resource('user', UserController::class);
Route::post('/user-store', [UserController::class, 'store'])->name('user.store');
Route::get('my-profile', [UserController::class, 'MyProfile'])->name('user.my-profile');
Route::get('settings', [UserController::class, 'openSettings'])->name('user.settings');
Route::get('user-profile/{id}', [UserController::class, 'UserProfile'])->name('user.user-profile');
Route::get('user-profile/{id}/media', [UserController::class, 'UserMedia'])->name('user.user-profile');
Route::get('users-management', [UserController::class, 'UserManagement'])->name('users-management');
Route::get('user-posts/{id}', [UserController::class, 'UserPosts'])->name('user.user-posts');
Route::post('user', [UserController::class, 'updateAccount'])->name('user.update-account');
Route::post('/update-role', [UserController::class, 'UpdateRole'])->name('user.update-role');
Route::post('/active-user', [UserController::class, 'ActiveUser'])->name('user.active-user');
Route::post('/active-code', [UserController::class, 'ActiveCode'])->name('user.active-code');
Route::resource('university', UniversityController::class);
Route::post('/show-students', [UniversityController::class, 'showStudent'])->name('university.students');
Route::post('/show-courses', [UniversityController::class, 'showCourse'])->name('university.courses');
Route::resource('college', CollegeController::class);
Route::get('/college-search', [CollegeController::class, 'search'])->name('college-search');
Route::resource('department', DepartmentController::class);
Route::get('/department-search', [DepartmentController::class, 'search'])->name('department-search');
Route::resource('building', BuildingController::class);
Route::get('/building-search', [BuildingController::class, 'search'])->name('building-search');
Route::resource('hall', HallController::class);
Route::get('/hall-search', [HallController::class, 'search'])->name('hall-search');
Route::resource('teacher', TeacherController::class);
Route::get('/teacher-search', [TeacherController::class, 'search'])->name('teacher-search');
Route::get('/get-departments/{college_id}', [TeacherController::class, 'getDepartments']);
Route::resource('students-data', StudentsDataController::class);
Route::get('upload-students/{id}', [StudentsDataController::class, 'uploadStudents'])->name('student.upload-students'); 
Route::resource('students', StudentController::class);
Route::post('students', [StudentController::class, 'updateData'])->name('students.update-data');
Route::get('/get-department-s/{college_id}', [StudentsDataController::class, 'getDepartments']);
Route::get('/get-student-data', [StudentsDataController::class, 'getData']);
Route::resource('colleges_buildings', College_BuildingsController::class);
Route::resource('role', RoleController::class);
Route::get('/role-search', [RoleController::class, 'search'])->name('role-search');
Route::resource('course', CourseController::class);
Route::get('/course-search', [CourseController::class, 'search'])->name('course-search');
Route::get('/get-departments-course/{college_id}', [CourseController::class, 'getDepartments']);
Route::resource('academic-year', AcademicYearController::class);
Route::resource('schedule', SchedulesController::class);
Route::get('/schedule-search', [SchedulesController::class, 'search'])->name('schedule-search');
Route::get('/get-departments-schedule/{college_id}', [SchedulesController::class, 'getDepartments']);
Route::resource('category', CategoryController::class);
Route::get('/category-search', [CategoryController::class, 'search'])->name('category-search');
Route::resource('contactInfo', ContactInfoController::class);
Route::resource('posts', PostController::class);
Route::get('/get-posts', [PostController::class, 'getPosts'])->name('get-posts');
Route::get('/posts-management', [PostController::class, 'PostsManagement'])->name('posts-management');
Route::get('/get-comments', [PostController::class, 'getComments'])->name('get-comments');
Route::get('/get-interaction', [PostController::class, 'GetInteraction']);
Route::post('/post/record-interaction', [PostController::class, 'recordInteraction']);
Route::post('/post/like-interaction', [PostController::class, 'likeInteraction']);
Route::post('/post/comment-interaction', [PostController::class, 'commentInteraction']);
Route::post('/post/save-interaction', [PostController::class, 'saveInteraction']);
Route::post('/delete-post', [PostController::class, 'deletePost']);
Route::resource('events', EventController::class);
Route::post('/change-password', [UserController::class, 'changePassword']);
Route::resource('chat', ChatController::class);
Route::get('/get-user/{id}', [ChatController::class, 'getUser'])->name('get.user');
Route::get('/get-chat/{id}', [ChatController::class, 'getChat'])->name('get-chat');
Route::get('/get-user-info/', [ChatController::class, 'getUserInof']);
Route::resource('contact', ContactController::class);
Route::post('/contacts-block', [ContactController::class, 'BlockUser'])->name('contact.block');
Route::post('/select-user', [ContactController::class, 'SelectUserType'])->name('contact.select-user');
Route::get('/contact-search', [ContactController::class, 'search'])->name('contact-search');
Route::post('/chat/read-chat', [ChatController::class, 'readChat']);
Route::get('/delete-chat/{id}', [ChatController::class, 'deleteChat'])->name('delete-chat');
Route::resource('library', LibraryController::class);
Route::get('/add-book', [LibraryController::class, 'AddBook']);
Route::post('/library/save-book', [LibraryController::class, 'SaveBookInfo']);
Route::post('/library/like-book', [LibraryController::class, 'LikeBookInfo']);
Route::post('/library/download-book', [LibraryController::class, 'DownloadBookInfo']);
Route::post('/library/open-book', [LibraryController::class, 'OpenBookInfo']);
Route::post('/search', [LibraryController::class, 'search'])->name('library.search');
Route::get('/get-book-info', [LibraryController::class, 'GetBookInfo']);
Route::get('/my-book', [LibraryController::class, 'GetMyBook']);
Route::post('/get-book', [LibraryController::class, 'GetBook'])->name('library.filter');
Route::post('/stop-book', [LibraryController::class, 'StopBook'])->name('library.stop-book');
Route::get('/books', [LibraryController::class, 'BookManagement'])->name('library.management');
Route::resource('send-message', ContactMessageController::class);
Route::post('/user/auth-account', [UserController::class, 'AuthAccount']);
Route::post('/user/create-account', [UserController::class, 'createAccount'])->name('user.account');
Route::get('/my-lecture', [SchedulesController::class, 'GetTeacherSchedule'])->name('schedule.get');
Route::get('/student-schedule', [SchedulesController::class, 'GetStudentSchedule'])->name('schedule.student-schedule');
Route::get('/tomorrow', [SchedulesController::class, 'GetTomorrowSchedule'])->name('schedule.tomorrow');
Route::get('/tomorrow-schedule', [SchedulesController::class, 'GetStudentTomorrowSchedule'])->name('schedule.tomorrow-schedule');
Route::get('/level', [SchedulesController::class, 'GetLevelsSchedule'])->name('schedule.level');
Route::resource('periods', PeriodController::class);
Route::post('/period-update/{id}', [PeriodController::class, 'updatePeriod'])->name('periods.update');
Route::post('/my-students', [TeacherController::class, 'MyStudents'])->name('teacher.my-students');
Route::post('/put-grade', [TeacherController::class, 'putGrade'])->name('teacher.put-grade');
Route::resource('exam', ExamController::class);
Route::get('/add-exam', [ExamController::class, 'AddExam'])->name('exam.add');
Route::get('/list-students/{id}', [ExamController::class, 'ListStudents'])->name('exam.list-students');
Route::post('/exam-result', [ExamController::class, 'ExamResult'])->name('exam.result');
Route::get('/my-exam', [ExamController::class, 'MyExam'])->name('exam.my-exam');
Route::get('/show-my-exam/{id}', [ExamController::class, 'ShowMyExam'])->name('exam.show-my-exam');
Route::resource('notification', NotificationController::class);
Route::post('/notification-responses', [NotificationController::class, 'NotificationResponses'])->name('notification.responses');
Route::get('/hall-booking', [HallController::class, 'HallBooking'])->name('hall.hall-booking');
Route::resource('attendances', AttendanceController::class);
Route::get('/get-students', [AttendanceController::class, 'GetStudents'])->name('get-students');
Route::post('/chack-lecture', [AttendanceController::class, 'CheckLecture'])->name('attendances.chack');
Route::post('/attendances-record', [AttendanceController::class, 'GetStudentsAttendancesRecord'])->name('attendances.record');
Route::get('/my-attendances-record', [AttendanceController::class, 'GetMyAttendanceRecord']);
Route::resource('channel', ChannelController::class);

Route::post('/put-schedule', function (Request $request) {
    $colleges = College::all();
    $departments = Department::all();
    $buildings = Building::all();
    $id = DB::table('colleges_buildings')->where('college_id', $request->college_id)->get('building_id')->pluck('building_id');
    $halls = Hall::all()->whereIn('building_id', $id);
    $teachers = Teacher::all()->where('college_id', $request->college_id);
    $academic_years = AcademicYear::orderBy('id', 'desc')->get();
    $courses = Course::all()->where('department_id', $request->department_id)->where('level', $request->level)->whereIn('type', json_decode($request->type))->where('term', $request->term);
    
    
    $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
    $types = ['متطلب','مقرر','عام'];
    $terms = ['الاول','الثاني'];
    $days = ['السبت','الاحد','الاثنين','الثلاثاء','الاربعاء','الخميس'];
    $department_id = $request->department_id;
    $academic_year = $request->academic_year;
    $level = $request->level;
    $term = $request->term;

    return view('admins.setup.put-schedule', [
        'colleges' => $colleges,
        'departments' => $departments,
        'buildings' => $buildings,
        'halls' => $halls,
        'teachers' => $teachers,
        'academic_years' => $academic_years,
        'levels' => $levels,
        'types' => $types,
        'terms' => $terms,
        'courses' => $courses,
        'days' => $days,
        'department_id' => $department_id,
        'academic_year' => $academic_year,
        'level' => $level,
        'term' => $term,
    ]);
});
