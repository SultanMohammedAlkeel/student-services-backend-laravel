<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationCodeMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * جلب بيانات الطالب
     */
    public function getstudent($id = null)
    {
        try {
            // إذا لم يتم تقديم ID، جلب الطالب الحالي
            if ($id === null) {
                $user = Auth::user();
                if (!$user || $user->user !== 'طالب') {
                    return response()->json([
                        'success' => false,
                        'message' => 'المستخدم الحالي ليس طالباً'
                    ], 403);
                }
                $id = $user->user_id;
            }

            $student = Student::with(['user'])->find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الطالب'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatStudentData($student)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تسجيل مستخدم جديد
     */
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email',
    //         'student_id' => 'required|string',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // البحث عن الطالب باستخدام البريد الإلكتروني
    //     $student = Student::where('email', $request->email)->first();

    //     if (!$student) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'لا يوجد طالب مسجل بهذا البريد الإلكتروني'
    //         ], 404);
    //     }

    //     // إنشاء رمز التفعيل
    //     $activationCode = ActivationCode::create([
    //         'code' => ActivationCode::generateCode(),
    //         'is_used' => false,
    //         'is_student' => true,
    //         'account_id' => $student->id,
    //         'expires_at' => now()->addDays(3),
    //         'sent_to' => $request->email
    //     ]);

    //     // إرسال رمز التفعيل بالبريد
    //     try {
    //         Mail::to($request->email)->send(new ActivationCodeMail($activationCode));
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'تم إرسال رمز التفعيل إلى بريدك الإلكتروني',
    //             'student_id' => $student->id
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'حدث خطأ أثناء إرسال رمز التفعيل',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * تفعيل الحساب باستخدام الكود
    //  */
    // public function verifyCode(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'code' => 'required|string',
    //         'student_id' => 'required|integer',
    //         'username' => 'required|string',
    //         'password' => 'required|string',
    //         'email' => 'required|email'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $activationCode = ActivationCode::where('code', $request->code)
    //         ->where('is_student', true)
    //         ->where('is_used', false)
    //         ->where('expires_at', '>', now())
    //         ->first();

    //     if (!$activationCode) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'رمز التفعيل غير صالح أو منتهي الصلاحية'
    //         ], 400);
    //     }

    //     try {
    //         $user = User::create([
    //             'name' => $request->username,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //             'role_id' => 3,
    //             'user' => 'طالب',
    //             'user_id' => $activationCode->account_id
    //         ]);

    //         $activationCode->update(['is_used' => true]);

    //         // جلب بيانات الطالب المرتبطة
    //         $student = Student::find($activationCode->account_id);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'تم إنشاء الحساب بنجاح',
    //             'user' => $this->formatUserData($user),
    //             'student' => $student ? $this->formatStudentData($student) : null,
    //             'token' => $user->createToken('auth_token')->plainTextToken
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'حدث خطأ أثناء إنشاء الحساب',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * تسجيل الدخول
     */
    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         $user = User::where('email', $request->email)->first();

    //         if (!$user || !Hash::check($request->password, $user->password)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'بيانات الدخول غير صحيحة'
    //             ], 401);
    //         }

    //         // حذف أي توكنات قديمة للمستخدم
    //         $user->tokens()->delete();

    //         // جلب بيانات الطالب إذا كان المستخدم طالباً
    //         $student = null;
    //         if ($user->user === 'طالب' && $user->user_id) {
    //             $student = Student::find($user->user_id);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'تم تسجيل الدخول بنجاح',
    //             'user' => $this->formatUserData($user),
    //             'student' => $student ? $this->formatStudentData($student) : null,
    //             'token' => $user->createToken('auth_token')->plainTextToken
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'حدث خطأ أثناء تسجيل الدخول',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);

    // البحث عن المستخدم بالبريد الإلكتروني
    $user = User::where('email', $request->email)->first();

    // التحقق من وجود المستخدم وصحة كلمة المرور
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'بيانات الدخول غير صحيحة'
        ], 401);
    }

    // حذف أي توكنات قديمة للمستخدم
    $user->tokens()->delete();

    return response()->json([
        'message' => 'تم تسجيل الدخول بنجاح',
        'user' => $user,
        'token' => $user->createToken('auth_token')->plainTextToken
    ], 200);
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
     * جلب بيانات المستخدم الحالي
     */
    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();
            $student = null;

            if ($user->user === 'طالب' && $user->user_id) {
                $student = Student::find($user->user_id);
            }

            return response()->json([
                'success' => true,
                'user' => $this->formatUserData($user),
                'student' => $student ? $this->formatStudentData($student) : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات المستخدم',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تنسيق بيانات المستخدم للاستجابة
     */
    private function formatUserData(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'user' => $user->user,
            'user_id' => $user->user_id,
            'phone_number' => $user->phone_number,
            'image_url' => $user->image_url,
            'status' => $user->status,
            'last_login' => $user->last_login,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];
    }

    /**
     * تنسيق بيانات الطالب للاستجابة
     */
    private function formatStudentData(Student $student)
    {
        return [
            'id' => $student->id,
            'full_name' => $student->full_name,
            'gender' => $student->gender,
            'address' => $student->address,
            'date_of_birth' => $student->date_of_birth,
            'enrollment_year' => $student->enrollment_year,
            'level' => $student->level,
            'department_id' => $student->department_id,
            'email' => $student->email,
            'created_at' => $student->created_at,
            'updated_at' => $student->updated_at
        ];
    }
}