<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'type',
        'status',
        'expires_at',
        'used_at',
        'attempts',
        'max_attempts',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * العلاقة مع نموذج المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * التحقق من صحة الرمز
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && 
               $this->expires_at > now() && 
               $this->attempts < $this->max_attempts;
    }

    /**
     * التحقق من انتهاء صلاحية الرمز
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * التحقق من استخدام الرمز
     */
    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    /**
     * تحديث حالة الرمز إلى مستخدم
     */
    public function markAsUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    /**
     * تحديث حالة الرمز إلى منتهي الصلاحية
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * زيادة عدد المحاولات
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        
        // إذا وصل عدد المحاولات للحد الأقصى، قم بتعطيل الرمز
        if ($this->attempts >= $this->max_attempts) {
            $this->markAsExpired();
        }
    }

    /**
     * إنشاء رمز تفعيل جديد
     */
    public static function generateCode(
        string $email, 
        string $type = 'password_reset',
        int $expiryMinutes = 3,
        string $ipAddress = null,
        string $userAgent = null
    ): self {
        // إلغاء جميع الرموز السابقة لنفس البريد الإلكتروني والنوع
        self::where('email', $email)
            ->where('type', $type)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // إنشاء رمز جديد
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'email' => $email,
            'code' => $code,
            'type' => $type,
            'status' => 'pending',
            'expires_at' => now()->addMinutes($expiryMinutes),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * التحقق من صحة الرمز والبريد الإلكتروني
     */
    public static function verifyCode(string $email, string $code, string $type = 'password_reset'): ?self
    {
        $resetCode = self::where('email', $email)
            ->where('code', $code)
            ->where('type', $type)
            ->where('status', 'pending')
            ->first();

        if (!$resetCode) {
            return null;
        }

        // التحقق من انتهاء الصلاحية
        if ($resetCode->isExpired()) {
            $resetCode->markAsExpired();
            return null;
        }

        // التحقق من عدد المحاولات
        if ($resetCode->attempts >= $resetCode->max_attempts) {
            $resetCode->markAsExpired();
            return null;
        }

        return $resetCode;
    }

    /**
     * تنظيف الرموز المنتهية الصلاحية
     */
    public static function cleanupExpiredCodes(): int
    {
        return self::where('expires_at', '<', now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }

    /**
     * Scope للرموز الصالحة
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now())
                    ->whereColumn('attempts', '<', 'max_attempts');
    }

    /**
     * Scope للرموز المنتهية الصلاحية
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '<=', now())
              ->orWhere('status', 'expired')
              ->orWhereColumn('attempts', '>=', 'max_attempts');
        });
    }
}

