<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Chat_messages;
use App\Models\College;
use App\Models\Contact;
use App\Models\ContactInfo;
use App\Models\Course;
use App\Models\Department;
use App\Models\Period;
use App\Models\Post;
use App\Models\users;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        var $icons = [
            'phone' => '<span class="fas fa-phone" style="color: #0088cc"></span>',
            'email' => '<span class="fas fa-envelope text-danger"></span>',
            'telegram' => '<span class="fab fa-telegram" style="color: #0088cc"></span>',
            'whatsapp' => '<span class="fab fa-whatsapp" style="color: #25D366"></span>',
            'facebook' => '<span class="fab fa-facebook" style="color: #1877F2"></span>',
            'x twitter' => '<span class="fab fa-x-twitter text-dark"></span>',
            'instagram' => '<span class="fab fa-instagram" style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></span>',
            'linkedin' => '<span class="fab fa-linkedin" style="color: #0A66C2"></span>',
            'tiktok' => '<span class="fab fa-tiktok" style="color: #000000"></span>',
            'youtube' => '<span class="fab fa-youtube text-danger"></span>',
            'snapchat' => '<span class="fab fa-snapchat" style="color: #FFFC00"></span>',
            'website' => '<span class="fas fa-globe text-info"></span>',
            'other' => '<span class="fas fa-ellipsis-h text-secondary"></span>'
        ];
    public function index()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $users = User::where('user',  'مشرف')->get();
        $roles = Role::all();
        $genders = ['ذكر','انثى'];
        return view('admins.setup.users', ['users' => $users, 'roles' => $roles, 'genders' => $genders]);
    }

    public function UserManagement()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        
        return view('admins.management.users', [
            'users' => User::all(), 
            'roles' =>  Role::all(),
            'techers' => Teacher::all(),
            'students' => Student::all(),
            'department' => Department::all(),
            'college' => College::all()
        ]);
    }

    public function UpdateRole(Request $request)  
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $user = User::find($request->id);
        $user->role_id = $request->role_id;
        $user->update();
        return redirect()->back();    
    }

    public function ActiveUser(Request $request)  
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $user = User::find($request->id);

        $user->is_active = $user->is_active == 1 ? 0 : 1;
        $user->update();
        return redirect()->back();    
    }
    
    public function ActiveCode(Request $request)  
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $user = Student::find($request->id);

        $user->active_code = rand(1000000, 9999999);
        $user->timeout = now()->addDay();
        $user->update();
        return redirect()->back();    
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function createAccount(Request $request)
    {
        $users = new User();
        $validateUser = User::where('user', session('user_type'))->where('user_id', $request->id)->first();

        if ($validateUser) {
            return redirect()->back()->with('erorr', $validateUser);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $id = '';
        $imageName = '';
                
        if (isset($users->all()->last()->id)) {
            $id = $users->all()->last()->id + 1;
        } else {
            $id = 1;
        }
        
        if (request()->hasFile('image_url')) {
            $image = $request->file('image_url');
            $imgName = $id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('', $imgName, 'profile');
            $imageName = 'images/profiles/' . $path;
        } else {
            $imageName = 'images/profiles/default.png';
            
        }
        
        $users->code = rand(1000000, 9999999);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->image_url = $imageName;
        $users->phone_number = $request->phone_number;
        $users->role_id = session('user_type') == 'معلم'? 3 : 4;
        $users->user = session('user_type');
        $users->last_login = now();
        $users->user_id = $request->id;
        if (session('user_type') == 'معلم') {
            $users->gender = Teacher::find($request->id)->gender;
        } else {
            $users->gender = Student::find($request->id)->gender;
        }
        $users->save();

        if (session('user_type') == 'معلم') {
            $teacher = Teacher::find($request->id);
            $teacher->is_login = 1;
            $teacher->is_used = 1;
            $teacher->updated_at = now();
            $teacher->save();
        } else {
            $student = Student::find($request->id);
            $student->is_login = 1;
            $student->is_used = 1;
            $student->updated_at = now();
            $student->save();
        }

        // معلومات الاتصال البريد
        $email = new ContactInfo();
        $email->user_id  = $id;
        $email->platform = 'email';
        $email->url = $request->email;
        $email->save();

        // معلومات الاتصال الهاتف
        $phone = new ContactInfo();
        $phone->user_id  = $id;
        $phone->platform = 'phone';
        $phone->url = $request->phone_number;
        $phone->save();
        if (session('user_type') == 'معلم') {
            return redirect('/teacher-sign-in');
        } else {
            return redirect('/student-sign-in');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $users = new User();
        $role = new Role();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $id = '';
        $imageName = '';


        if ($role->count() == 0) {
            
            $role = new Role();
            $role->name = 'المشرف العام'; 
            $role->type = 'مشرف';
            $role->description = 'يشرف على النظام والصلاحيات العامة';
            $role->save();

            $role = new Role();
            $role->name = 'مشرف'; 
            $role->type = 'مشرف';
            $role->description = 'يشرف على إدارة جزء معين من النظام';
            $role->save();

            $role = new Role();
            $role->name = 'معلم'; 
            $role->type = 'معلم';
            $role->description = 'معلم ومشرف على فصل دراسي أو أكثر';
            $role->save();

            $role = new Role();
            $role->name = 'طالب'; 
            $role->type = 'طالب';
            $role->description = 'طالب يتبع لكلية وقسم معين';
            $role->save();

            $role = new Role();
            $role->name = 'عميد كلية'; 
            $role->type = 'معلم';
            $role->description = 'يشرف على الكلية وأقسامها';
            $role->save();

            $role = new Role();
            $role->name = 'رئيس قسم'; 
            $role->type = 'معلم';
            $role->description = 'يشرف على القسم الأكاديمي داخل الكلية';
            $role->save();

            $role = new Role();
            $role->name = 'مندوب'; 
            $role->type = 'طالب';
            $role->description = 'يمثل طلاب القسم أمام الإدارة';
            $role->save();

            $role = new Role();
            $role->name = 'رئيس اللجنة العلمية'; 
            $role->type = 'طالب';
            $role->description = 'يقود اللجنة العلمية الطلابية وينسق الأنشطة الأكاديمية للطلاب';
            $role->save();

            $role = new Role();
            $role->name = 'عضو اللجنة العلمية'; 
            $role->type = 'طالب';
            $role->description = 'يشارك في أنشطة اللجنة العلمية ويدعم المبادرات الأكاديمية للطلاب';
            $role->save();




            if (isset($users->all()->last()->id)) {
                $id = $users->all()->last()->id + 1;
            } else {
                $id = 1;
            }
            $image = $request->file('image_url');
            $imgName = $id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('', $imgName, 'profile');
            $imageName = 'images/profiles/' . $path;

            $users->code = rand(1000000, 9999999);
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $users->image_url = $imageName;
            $users->gender = $request->gender;
            $users->phone_number = $request->phone_number;
            $users->role_id = 1;
            $users->user = 'مشرف';
            $users->last_login = now();
            $users->save();

            
            // معلومات الاتصال البريد
            $email = new ContactInfo();
            $email->user_id  = $id;
            $email->platform = 'email';
            $email->url = $request->email;
            $email->save();

            // معلومات الاتصال الهاتف
            $phone = new ContactInfo();
            $phone->user_id  = $id;
            $phone->platform = 'phone';
            $phone->url = $request->phone_number;
            $phone->save();

            return redirect('/setup');
        }

        
        if (isset($users->all()->last()->id)) {
            $id = $users->all()->last()->id + 1;
        } else {
            $id = 1;
        }

        $image = $request->file('image_url');
        $imgName = $id . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('', $imgName, 'profile');
        $imageName = 'images/profiles/' . $path;
        
        $users->code = rand(1000000, 9999999);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->image_url = $imageName;
        $users->phone_number = $request->phone_number;
        $users->role_id = $request->role_id;
        $users->gender = $request->gender;
        $users->user = 'مشرف';
        $users->last_login = now();
        $users->save();

        // معلومات الاتصال البريد
        $email = new ContactInfo();
        $email->user_id  = $id;
        $email->platform = 'email';
        $email->url = $request->email;
        $email->save();

        // معلومات الاتصال الهاتف
        $phone = new ContactInfo();
        $phone->user_id  = $id;
        $phone->platform = 'phone';
        $phone->url = $request->phone_number;
        $phone->save();

        return redirect()->back();
    }

    public function changePassword(Request $request) {
        $last = $request->lastPassword;
        $user = User::find(session('user_id'));
        $validator = Validator::make($request->all(), [
            'lastPassword' => 'required',
            'newPassword' => 'required',
            'againPassword' => 'required'
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$this->checkLastPassword($request->lastPassword)) {
                $validator->errors()->add('lastPassword', 'كلمة المرور القديمة غير صحيحة');
            }
        });
        $validator->after(function ($validator) use ($request) {
            if (!$this->checkPassword($request->newPassword, $request->againPassword)) {
                $validator->errors()->add('password', 'كلمة المرور غير متطابقة');
            }
        });
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->password = Hash::make($request->newPassword);
        $user->update();
        if (session('user_type') == 'مشرف') {
            return redirect('/admin-sign-in');
        }
        else if (session('user_type') == 'معلم') {
            return redirect('/teacher-sign-in');
        } else {
            return redirect('/student-sign-in');
        }
        
    }
    private function checkLastPassword($lastPassword)
    {
        $user = User::find(session('user_id'));
        // قم بالتحقق من أن lastPassword صحيحة
        // مثلاً: مقارنتها مع كلمة المرور الحالية في قاعدة البيانات
        return Hash::check($lastPassword, $user->password);
    }

    private function checkPassword($onePassword, $towPassword)
    {
        // قم بالتحقق من أن lastPassword صحيحة
        // مثلاً: مقارنتها مع كلمة المرور الحالية في قاعدة البيانات
        return $onePassword == $towPassword;
    }

    public function AuthAccount(Request $request)  {
        if (session('user_type') == 'معلم') {
            $teacher = Teacher::where('code', $request->code)->where('code', $request->active_code)->first();
            
            if ($teacher != null) {
                $is_login = Teacher::find($teacher->id)->is_login;
                
                if ($is_login) {
                    $name = User::where('user_id', $teacher->id)->where('user', session('user_type'))->first()->name;
                    return response()->json([
                        'is_login' => false,
                        'message' => 'تم تفعيل الحساب بالفعل باسم: (' . $name . ')'
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'id' => $teacher->id,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'كود التفعيل غير صحيح'
                ]);
            }
        } else {
            $student = Student::where('card', $request->code)->where('active_code', $request->active_code)->first();
            if ($student != null) {
                $is_login = Student::find($student->id)->is_login;
                if ($is_login) {
                    $name = User::where('user_id', $student->id)->where('user', session('user_type'))->first()->name;
                    return response()->json([
                        'is_login' => false,
                        'message' => 'تم تفعيل الحساب بالفعل باسم: (' . $name . ')'
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'id' => $student->id,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'كود التفعيل غير صحيح'
                ]);
            }
        }
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


    function generateLink($platform, $value) {
        $prefixes = [
            'phone' => 'tel:',
            'email' => 'mailto:',
            'telegram' => 'https://t.me/',
            'whatsapp' => 'https://wa.me/',
            'facebook' => 'https://www.facebook.com/',
            'twitter' => 'https://twitter.com/',
            'instagram' => 'https://www.instagram.com/',
            'linkedin' => 'https://www.linkedin.com/in/',
            'tiktok' => 'https://www.tiktok.com/@',
            'youtube' => 'https://www.youtube.com/',
            'snapchat' => 'https://www.snapchat.com/add/',
            'website' => '',
            'other' => ''
        ];
        
        return isset($prefixes[$platform]) ? $this->ensureFullURL($prefixes[$platform], $value) : $value;
    }
    
    function ensureFullURL($prefix, $value) {
        // التحقق مما إذا كان الرابط يحتوي بالفعل على البادئة
        if (strpos($value, $prefix) === 0 || filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return $prefix . $value;
    }
    

    public function UserProfile(string $id) 
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $roles = Role::all();
        $user = User::where('code', $id)->first();
        $contactInfos = ContactInfo::where('user_id', $user->id)->get();
        $contactInfos->transform(function ($contactInfo) {
            $contactInfo->url = $this->generateLink($contactInfo->platform, $contactInfo->url);
            return $contactInfo;
        });
    
        
        $contact = Contact::where('user_id', session('user_id'))->count();
        $friend = Contact::where('friend_id', session('user_id'))->count();
        
        $Period = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $courses = Course::where('department_id', session('department_id'))
                    ->where('level', session('level'))
                    ->where('term', $Period->first()->term)
                    ->get();
        $student = Student::where('id', $user->user_id)->first();

        return view('profile.user-profile', [
            'user' => $user,
            'roles' => $roles,
            'contactInfos' => $contactInfos,
            'icons' => $this->icons,
            'contact' => $contact,
            'friend' => $friend,
            'college' => College::all(),
            'department' => Department::all(),
            'courses' => $courses,
            'record' => $this->GetMyAttendanceRecord($user->user_id),
            'schedules' => $this->GetTeacherSchedule($student->level, $student->department_id),
            'teacher' => Teacher::all(),
            'student' => $student,
            'is_profile' => true,
        ]);  
    }

    public function UserPosts(string $id) 
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $roles = Role::all();
        $user = User::where('code', $id)->first();
        $contactInfos = ContactInfo::where('user_id', $user->id)->get();
        $contactInfos->transform(function ($contactInfo) {
            $contactInfo->url = $this->generateLink($contactInfo->platform, $contactInfo->url);
            return $contactInfo;
        });
    

        $posts = Post::where('sender_id', $user->id)->get();
    
        return view('profile.user-profile', [
            'user' => $user,
            'roles' => $roles,
            'contactInfos' => $contactInfos,
            'icons' => $this->icons,
            'posts' => $posts,
            'all_posts' => true,
        ]);    
    }
    
    public function UserMedia(string $id) 
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $roles = Role::all();
        $user = User::where('code', $id)->first();
        $contactInfos = ContactInfo::where('user_id', $user->id)->get();
        $contactInfos->transform(function ($contactInfo) {
            $contactInfo->url = $this->generateLink($contactInfo->platform, $contactInfo->url);
            return $contactInfo;
        });
    
        

        $contact = Contact::where('user_id', session('user_id'))
        ->where('friend_id', $user->id)
        ->orWhere('friend_id', session('user_id'))
        ->where('user_id', $user->id)
        ->first();
        
        $media = Chat_messages::where('contact_id', $contact->id)
        ->where('has_media', 1)
        ->where('is_deleted', 0)
        ->get();
    
        return view('profile.user-profile', [
            'user' => $user,
            'roles' => $roles,
            'contactInfos' => $contactInfos,
            'icons' => $this->icons,
            'media' => $media,
            'is_media' => true,
        ]);    
    }

    public function MyProfile()
    {   
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $roles = Role::all();
        $user = User::where('id', session('user_id'))->first();
        $contactInfos = ContactInfo::where('user_id', $user->id)->get();
        $contactInfos->transform(function ($contactInfo) {
            $contactInfo->url = $this->generateLink($contactInfo->platform, $contactInfo->url);
            return $contactInfo;
        });

        $contact = Contact::where('user_id', session('user_id'))->count();
        $friend = Contact::where('friend_id', session('user_id'))->count();
    
        
        $Period = Period::whereDate('start_date', '<=', now())
                     ->whereDate('end_date', '>=', now())
                     ->get();
        $courses = Course::where('department_id', session('department_id'))
                    ->where('level', session('level'))
                    ->where('term', $Period->first()->term)
                    ->get();

        return view('profile.profile', [
            'roles' => $roles,
            'contactInfos' => $contactInfos,
            'icons' => $this->icons,
            'contact' => $contact,
            'friend' => $friend,
            'college' => College::all(),
            'department' => Department::all(),
            'courses' => $courses,
            'record' => $this->GetMyAttendanceRecord(session('user_ref_id')),
            'schedules' => $this->GetTeacherSchedule(session('level'), session('department_id')),
            'teacher' => Teacher::all(),
            'student' => Student::where('id', session('user_ref_id'))->first(),
        ]);
    }

    function GetMyAttendanceRecord($id) 
        {
        $activePeriods = Period::whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
        $myID = $id;

        
        // البحث عن جميع سجلات الحضور التي تحتوي على هذا الطالب
        $attendances = Attendance::whereRaw('JSON_CONTAINS(data, ?)', [json_encode(['id' => $myID])])
        ->where('academic_year_id', $activePeriods->first()->academic_year_id)
            ->get();
        
        // استخراج بيانات الطالب من كل سجل
        $results = $attendances->map(function ($attendance) use ($myID) {
            $students = json_decode($attendance->data, true);
            return [
                'attendance_id' => $attendance->id,
                'lecture_date' => $attendance->lecture_date,
                'lecture_number' => $attendance->lecture_number,
                'course_id' => $attendance->course_id,
                'teacher_id' => $attendance->teacher_id,
                'level' => $attendance->level,
                'name' => collect($students)->firstWhere('id', $myID)['name'],
                'status' => collect($students)->firstWhere('id', $myID)['status']
            ];
        });
        
        return $results;
    }

    function GetTeacherSchedule($level, $department_id)
    {   
        $activePeriods = Period::whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->get();

        $results = DB::table('schedules')
        ->where('term', $activePeriods->first()->term)
        ->where('academic_year_id', $activePeriods->first()->academic_year_id)
        ->where('department_id', $department_id)
        ->where('level', $level)
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

    public function openSettings() {
        $contactInfos = ContactInfo::where('user_id', session('user_id'))->get();
        $platforms = ['phone', 'email', 'telegram', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'tiktok', 'youtube', 'snapchat', 'website', 'other'];

        $contactInfos->transform(function ($contactInfo) {
            $contactInfo->url = $this->generateLink($contactInfo->platform, $contactInfo->url);
            return $contactInfo;
        });
        return view('profile.settings', [
            'id' => session('user_id'),
            'contactInfos' => $contactInfos,
            'icons' => $this->icons,
            'platforms' => $platforms,
            'college' => College::all(),
            'department' => Department::all()
        ]);
    }

    public function updateAccount(Request $request)
    {
        $imagePath = public_path($request->image);
        if ($request->file('image_url')) {
            if (File::exists($imagePath)) {
                $fileNameWithExtension = basename($imagePath);
                $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
                if ($fileName != 'default') {
                    File::delete($imagePath);
                }
                
                $user = User::find($request->id);
                $user->name = $request->name;
                $image      = $request->file('image_url');
                $imageName  = $request->id . '.' . $image->getClientOriginalExtension();
                $path       = $image->storeAs('', $imageName, 'profile');
                $user->image_url = 'images/profiles/' . $path;
                $user->save();
                session(['username' => $request->name]);
                session(['image_url' => 'images/profiles/' . $path]);
                return redirect()->back();
            } else {
                $user = User::find($request->id);
                $user->name = $request->name;
                $image = $request->file('image_url');
                $imageName = $request->id . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('', $imageName, 'profile');
                $user->image_url = 'images/profiles/' . $path;
                $user->save();
                session(['username' => $request->name]);
                session(['image_url' => 'images/profiles/' . $path]);
                return redirect()->back();
            }
        } else {
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->save();
            session(['username' => $request->name]);
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
