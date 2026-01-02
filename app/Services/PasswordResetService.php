<?php

namespace App\Services;

use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordResetCodeMail;

class PasswordResetService
{
    /**
     * إرسال رمز إعادة تعيين كلمة المرور
     */
    public function sendPasswordResetCode(string $email, string $ipAddress = null, string $userAgent = null): array
    {
        try {
            // التحقق من وجود المستخدم
            $user = User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'البريد الإلكتروني غير مسجل في النظام',
                    'code' => 'USER_NOT_FOUND'
                ];
            }

            // التحقق من عدم وجود رمز صالح مرسل مؤخراً (منع الإرسال المتكرر)
            $recentCode = PasswordResetCode::where('email', $email)
                ->where('status', 'pending')
                ->where('created_at', '>', now()->subMinute())
                ->first();

            if ($recentCode) {
                return [
                    'success' => false,
                    'message' => 'تم إرسال رمز التفعيل مؤخراً. يرجى الانتظار دقيقة واحدة قبل طلب رمز جديد',
                    'code' => 'TOO_MANY_REQUESTS'
                ];
            }

            // تنظيف الرموز المنتهية الصلاحية
            PasswordResetCode::cleanupExpiredCodes();

            // إنشاء رمز جديد (صالح لمدة 3 دقائق)
            $resetCode = PasswordResetCode::generateCode(
                $email,
                'password_reset',
                3, // 3 دقائق
                $ipAddress,
                $userAgent
            );

            // إرسال البريد الإلكتروني
            Mail::to($email)->send(new PasswordResetCodeMail($resetCode, $user));

            Log::info('Password reset code sent', [
                'email' => $email,
                'code_id' => $resetCode->id,
                'ip_address' => $ipAddress
            ]);

            return [
                'success' => true,
                'message' => 'تم إرسال رمز التفعيل إلى بريدك الإلكتروني',
                'expires_at' => $resetCode->expires_at->format('Y-m-d H:i:s'),
                'code' => 'CODE_SENT'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send password reset code', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال رمز التفعيل. يرجى المحاولة مرة أخرى',
                'code' => 'SEND_FAILED'
            ];
        }
    }

    /**
     * التحقق من صحة رمز إعادة تعيين كلمة المرور
     */
    public function verifyPasswordResetCode(string $email, string $code): array
    {
        try {
            // البحث عن الرمز
            $resetCode = PasswordResetCode::where('email', $email)
                ->where('code', $code)
                ->where('type', 'password_reset')
                ->first();

            if (!$resetCode) {
                return [
                    'success' => false,
                    'message' => 'رمز التفعيل غير صحيح',
                    'code' => 'INVALID_CODE'
                ];
            }

            // زيادة عدد المحاولات
            $resetCode->incrementAttempts();

            // التحقق من صحة الرمز
            if (!$resetCode->isValid()) {
                if ($resetCode->isExpired()) {
                    return [
                        'success' => false,
                        'message' => 'انتهت صلاحية رمز التفعيل. يرجى طلب رمز جديد',
                        'code' => 'CODE_EXPIRED'
                    ];
                }

                if ($resetCode->attempts >= $resetCode->max_attempts) {
                    return [
                        'success' => false,
                        'message' => 'تم تجاوز عدد المحاولات المسموحة. يرجى طلب رمز جديد',
                        'code' => 'MAX_ATTEMPTS_EXCEEDED'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'رمز التفعيل غير صالح',
                    'code' => 'INVALID_CODE'
                ];
            }

            Log::info('Password reset code verified', [
                'email' => $email,
                'code_id' => $resetCode->id
            ]);

            return [
                'success' => true,
                'message' => 'تم التحقق من رمز التفعيل بنجاح',
                'reset_token' => $this->generateResetToken($resetCode),
                'code' => 'CODE_VERIFIED'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to verify password reset code', [
                'email' => $email,
                'code' => $code,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء التحقق من رمز التفعيل',
                'code' => 'VERIFICATION_FAILED'
            ];
        }
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(string $email, string $code, string $newPassword): array
    {
        try {
            // التحقق من صحة الرمز مرة أخرى
        //    $verificationResult = $this->verifyPasswordResetCode($email, $code);
            
            // if (!$verificationResult['success']) {
            //     return $verificationResult;
            // }

            // البحث عن المستخدم
            $user = User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'المستخدم غير موجود',
                    'code' => 'USER_NOT_FOUND'
                ];
            }

            // تحديث كلمة المرور
            $user->update([
                'password' => bcrypt($newPassword)
            ]);

            // تحديث حالة الرمز إلى مستخدم
            $resetCode = PasswordResetCode::where('email', $email)
                ->where('code', $code)
                ->where('type', 'password_reset')
                ->where('status', 'pending')
                ->first();

            if ($resetCode) {
                $resetCode->markAsUsed();
            }

            Log::info('Password reset successfully', [
                'email' => $email,
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'message' => 'تم تغيير كلمة المرور بنجاح',
                'code' => 'PASSWORD_RESET_SUCCESS'
            ];

        } catch (\Exception $e) {
            Log::error('Failed to reset password', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير كلمة المرور',
                'code' => 'RESET_FAILED'
            ];
        }
    }

    /**
     * إنشاء رمز مؤقت للتحقق من صحة العملية
     */
    private function generateResetToken(PasswordResetCode $resetCode): string
    {
        return base64_encode(json_encode([
            'email' => $resetCode->email,
            'code_id' => $resetCode->id,
            'timestamp' => now()->timestamp
        ]));
    }

    /**
     * الحصول على إحصائيات رموز إعادة التعيين
     */
    public function getResetStatistics(string $email = null): array
    {
        $query = PasswordResetCode::query();
        
        if ($email) {
            $query->where('email', $email);
        }

        $stats = [
            'total_codes' => $query->count(),
            'pending_codes' => $query->where('status', 'pending')->count(),
            'used_codes' => $query->where('status', 'used')->count(),
            'expired_codes' => $query->where('status', 'expired')->count(),
            'recent_codes' => $query->where('created_at', '>', now()->subDay())->count(),
        ];

        return $stats;
    }
}

