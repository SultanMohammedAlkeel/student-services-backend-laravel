<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use App\Models\ContactInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function AuthAccount(Request $request)
    {
        // if (session('user_type') == 'معلم') {
        //     $teacher = Teacher::where('code', $request->code)->where('code', $request->active_code)->first();

        //     if ($teacher != null) {
        //         $is_login = Teacher::find($teacher->id)->is_login;

        //         if ($is_login) {
        //             $name = User::where('user_id', $teacher->id)->where('user', session('user_type'))->first()->name;
        //             return response()->json([
        //                 'is_login' => false,
        //                 'message' => 'تم تفعيل الحساب بالفعل باسم: (' . $name . ')'
        //             ]);
        //         }

        //         return response()->json([
        //             'success' => true,
        //             'id' => $teacher->id,
        //         ]);
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'كود التفعيل غير صحيح'
        //         ]);
        //     }
        // } else {
        $student = Student::where('card', $request->student_id)->where('active_code', $request->activation_code)->first();
        if ($student != null) {
            $is_login = Student::find($student->id)->is_login;
            if ($is_login) {
                $name = User::where('user_id', $student->id)->where('user', 'طالب')->first()->name;
                return response()->json([
                    'is_login' => true,
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


    public function register(Request $request)
    {
        // تسجيل بيانات الطلب لأغراض التصحيح

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'student_id' => 'required|string',
            'activation_code' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'username.required' => 'يجب إدخال اسم المستخدم',
            'username.max' => 'اسم المستخدم طويل جداً (الحد الأقصى 255 حرفاً)',
            'username.unique' => 'اسم المستخدم مسجل مسبقاً',

            'email.required' => 'يجب إدخال البريد الإلكتروني',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني طويل جداً',

            'password.required' => 'يجب إدخال كلمة المرور',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',

            'phone.required' => 'يجب إدخال رقم الهاتف',
            'phone.max' => 'رقم الهاتف طويل جداً',

            'student_id.required' => 'يجب إدخال الرقم الجامعي',

            'activation_code.required' => 'يجب إدخال رمز التفعيل',

            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'نوع الصورة غير مسموح به (المسموح: jpeg, png, jpg, gif)',
            'image.max' => 'حجم الصورة كبير جداً (الحد الأقصى 2MB)'
        ]);

        if ($validator->fails()) {
            $errorMessages = implode(' ', $validator->errors()->all());

            return response()->json([
                'success' => false, // تغيير إلى false لأن هناك أخطاء
                'message' => $errorMessages, // إرسال جميع رسائل الأخطاء في حقل message
            ], 422);
        }

        try {
            DB::beginTransaction();

            // البحث عن الطالب
            $student = Student::
                //with(['user', 'department'])
                where('card', $request->student_id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // التحقق من عدم وجود حساب مسبق
            if (User::where('user_id', $student->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account already exists for this student'
                ], 400);
            }

            // معالجة الصورة
            $imageName = 'default.png';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path('images/profiles'), $imageName);
            }

            // إنشاء المستخدم
            $user = new User();
            $user->code = rand(1000000, 9999999);
            $user->name = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->image_url = 'images/profiles/' . $imageName;
            $user->phone_number = $request->phone;
            $user->role_id = 4;
            $user->user = 'طالب';
            $user->last_login = now();
            $user->user_id = $student->id;
            $user->gender = $student->gender ?? 'ذكر';
            $user->save();

            // تحديث حالة الطالب
            $student->update([
                'is_login' => 1,
                'is_used' => 1,
                'updated_at' => now()
            ]);

            // معلومات الاتصال البريد
            $email = new ContactInfo();
            $email->user_id  = $user->id;
            $email->platform = 'email';
            $email->url = $request->email;
            $email->save();

            // معلومات الاتصال الهاتف
            $phone = new ContactInfo();
            $phone->user_id  = $user->id;
            $phone->platform = 'phone';
            $phone->url = $request->phone;
            $phone->save();

            // إنشاء توكن API
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحساب بنجاح',
                'token' => $token,
                'user' => $this->formatUserData($user),
                'student' => $this->formatStudentData($student),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء الحساب',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // دالة مساعدة لتنسيق بيانات المستخدم
    private function formatUserData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'image_url' => $user->image_url,
            'user_id' => $user->user_id,
        ];
    }

    // دالة مساعدة لتنسيق بيانات الطالب
    private function formatStudentData($student)
    {
        return [
            'id' => $student->id,
            'name' => $student->name,
            'gender' => $student->gender,
            'department' => $student->department->name,
            'enrollment_year' => $student->enrollment_year,
            'level' => $student->level,
            'address' => $student->address,
            'qualification' => $student->qualification,
            'date_of_birth' => $student->birth_date,
            'card' => $student->card,
        ];
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات الدخول غير صحيحة'
                ], 401);
            }

            // حذف أي توكنات قديمة للمستخدم
            $user->tokens()->delete();


            $student = Student::find($user->user_id);


            // إنشاء توكن API
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'token' => $token,
                'user' => $this->formatUserData($user),
                'student' => $this->formatStudentData($student),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في تسجيل الدخول ',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الخروج',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $request->user()->id,
            'phone_number' => 'sometimes|required|string|max:20',
            'current_password' => 'sometimes|required|string',
            'new_password' => 'sometimes|required|string|min:8|confirmed',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'يجب إدخال اسم المستخدم',
            'name.max' => 'اسم المستخدم طويل جداً (الحد الأقصى 255 حرفاً)',

            'email.required' => 'يجب إدخال البريد الإلكتروني',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني طويل جداً',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',

            'phone_number.required' => 'يجب إدخال رقم الهاتف',
            'phone_number.max' => 'رقم الهاتف طويل جداً',

            'current_password.required' => 'يجب إدخال كلمة المرور الحالية',
            'new_password.required' => 'يجب إدخال كلمة المرور الجديدة',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',

            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'نوع الصورة غير مسموح به (المسموح: jpeg, png, jpg, gif)',
            'image.max' => 'حجم الصورة كبير جداً (الحد الأقصى 2MB)'
        ]);

        if ($validator->fails()) {
            $errorMessages = implode(' ', $validator->errors()->all());
            return response()->json([
                'success' => false,
                'message' => $errorMessages,
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();

            // تحديث كلمة المرور إذا تم تقديمها
            if ($request->has('current_password') && $request->has('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'كلمة المرور الحالية غير صحيحة'
                    ], 400);
                }
                $user->password = Hash::make($request->new_password);
            }

            // تحديث الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة إذا لم تكن الصورة الافتراضية
                if ($user->image_url && $user->image_url !== 'images/profiles/default.png') {
                    $oldImagePath = public_path($user->image_url);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $user->id . '.' . $image->extension();
                $image->move(public_path('images/profiles'), $imageName);
                $user->image_url = 'images/profiles/' . $imageName;
            }

            // تحديث البيانات الأساسية
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
                // تحديث معلومات الاتصال للبريد الإلكتروني
                ContactInfo::where('user_id', $user->id)
                    ->where('platform', 'email')
                    ->update(['url' => $request->email]);
            }

            if ($request->has('phone_number')) {
                $user->phone_number = $request->phone_number;
                // تحديث معلومات الاتصال للهاتف
                ContactInfo::where('user_id', $user->id)
                    ->where('platform', 'phone')
                    ->update(['url' => $request->phone_number]);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البيانات بنجاح',
                'user' => $this->formatUserData($user),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث البيانات',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * رفع صورة الملف الشخصي
     */
    public function uploadProfileImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'image.required' => 'يجب اختيار صورة',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'نوع الصورة غير مسموح به (المسموح: jpeg, png, jpg, gif)',
            'image.max' => 'حجم الصورة كبير جداً (الحد الأقصى 2MB)'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();

            // حذف الصورة القديمة إذا لم تكن الصورة الافتراضية
            if ($user->image_url && $user->image_url !== 'images/profiles/default.png') {
                $oldImagePath = public_path($user->image_url);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $user->id . '.' . $image->extension();
            $image->move(public_path('images/profiles'), $imageName);

            $user->image_url = 'images/profiles/' . $imageName;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث صورة الملف الشخصي بنجاح',
                'image_url' => $user->image_url,
                'user' => $this->formatUserData($user),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile Image Upload Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في رفع الصورة',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * الحصول على بيانات المستخدم الحالي
     */
    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();
            $student = Student::find($user->user_id);

            return response()->json([
                'success' => true,
                'user' => $this->formatUserData($user),
                'student' => $this->formatStudentData($student),
            ]);
        } catch (\Exception $e) {
            Log::error('Get Current User Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب بيانات المستخدم',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * تحديث معلومات الاتصال
     */
    public function updateContactInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $request->user()->id,
            'phone_number' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
        ], [
            'email.required' => 'يجب إدخال البريد الإلكتروني',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'phone_number.required' => 'يجب إدخال رقم الهاتف',
            'address.max' => 'العنوان طويل جداً (الحد الأقصى 500 حرف)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();

            // تحديث البريد الإلكتروني
            if ($request->has('email')) {
                $user->email = $request->email;
                ContactInfo::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => 'email'],
                    ['url' => $request->email]
                );
            }

            // تحديث رقم الهاتف
            if ($request->has('phone_number')) {
                $user->phone_number = $request->phone_number;
                ContactInfo::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => 'phone'],
                    ['url' => $request->phone_number]
                );
            }

            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث معلومات التواصل بنجاح',
                'user' => $this->formatUserData($user),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Contact Info Update Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث معلومات التواصل',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'يجب إدخال كلمة المرور الحالية',
            'new_password.required' => 'يجب إدخال كلمة المرور الجديدة',
            'new_password.min' => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'كلمة المرور الحالية غير صحيحة'
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            // حذف جميع التوكنات الحالية لإجبار المستخدم على تسجيل الدخول مرة أخرى
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول مرة أخرى.',
            ]);
        } catch (\Exception $e) {
            Log::error('Password Change Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'فشل في تغيير كلمة المرور',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
