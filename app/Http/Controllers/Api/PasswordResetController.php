<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\PasswordResetService; // تأكد من أن هذا المسار صحيح لخدمتك
use App\Mail\PasswordResetCodeMail; // تأكد من أن هذا المسار صحيح لـ Mailable الخاص بك
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * إرسال رمز إعادة تعيين كلمة المرور
     */
    public function sendResetCode(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.exists' => 'البريد الإلكتروني غير مسجل في النظام',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // جلب المستخدم
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'البريد الإلكتروني غير مسجل في النظام',
                    'code' => 'USER_NOT_FOUND'
                ], 404);
            }

            // إرسال رمز إعادة التعيين باستخدام الخدمة المعدلة
            $result = $this->passwordResetService->sendPasswordResetCode(
                $request->email,
                $request->ip(),
                $request->userAgent()
            );

            $statusCode = $result['success'] ? 200 : 400;

            return response()->json($result, $statusCode);

        } catch (\Exception $e) {
            Log::error('Password reset code send failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم. يرجى المحاولة مرة أخرى',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * التحقق من صحة رمز إعادة تعيين كلمة المرور
     */
    public function verifyResetCode(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'code' => 'required|string|size:6',
            ], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.exists' => 'البريد الإلكتروني غير مسجل في النظام',
                'code.required' => 'رمز التفعيل مطلوب',
                'code.size' => 'رمز التفعيل يجب أن يكون 6 أرقام',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // التحقق من صحة الرمز
            $result = $this->passwordResetService->verifyPasswordResetCode(
                $request->email,
                $request->code
            );

            $statusCode = $result['success'] ? 200 : 400;

            return response()->json($result, $statusCode);

        } catch (\Exception $e) {
            Log::error('Password reset code verification failed', [
                'email' => $request->email,
                'code' => $request->code,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم. يرجى المحاولة مرة أخرى',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات
            $validator = Validator(
                $request->all(),
                [
                    'email' => 'required|email|exists:users,email',
                    'code' => 'required|string|size:6',
                    'password' => 'required|string|min:8|confirmed',
                ],
                [
                    'email.required' => 'البريد الإلكتروني مطلوب',
                    'email.email' => 'البريد الإلكتروني غير صحيح',
                    'email.exists' => 'البريد الإلكتروني غير مسجل في النظام',
                    'code.required' => 'رمز التفعيل مطلوب',
                    'code.size' => 'رمز التفعيل يجب أن يكون 6 أرقام',
                    'password.required' => 'كلمة المرور مطلوبة',
                    'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
                    'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // إعادة تعيين كلمة المرور
            $result = $this->passwordResetService->resetPassword(
                $request->email,
                $request->code,
                $request->password
            );

            $statusCode = $result['success'] ? 200 : 400;

            return response()->json($result, $statusCode);

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم. يرجى المحاولة مرة أخرى',
                'code' => 'SERVER_ERROR'
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات رموز إعادة التعيين (للمطورين فقط)
     */
    public function getResetStatistics(Request $request): JsonResponse
    {
        try {
            // التحقق من صلاحيات المطور (يمكن تخصيصها حسب الحاجة)
            if (!$request->user() || $request->user()->role_id !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول لهذه البيانات',
                ], 403);
            }

            $email = $request->query('email');
            $stats = $this->passwordResetService->getResetStatistics($email);

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get reset statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
            ], 500);
        }
    }

    /**
     * إلغاء جميع رموز إعادة التعيين لبريد إلكتروني معين
     */
    public function cancelResetCodes(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'email.exists' => 'البريد الإلكتروني غير مسجل في النظام',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            // إلغاء جميع الرموز المعلقة
            $cancelledCount = \App\Models\PasswordResetCode::where('email', $request->email)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء جميع رموز إعادة التعيين',
                'cancelled_count' => $cancelledCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel reset codes', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
            ], 500);
        }
    }
}


