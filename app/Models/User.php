<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'user', 'user_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // علاقة مع الطالب
    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id')
            ->where('user', 'طالب');
    }

    // علاقة مع المعلم
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'user_id')
            ->where('user', 'معلم');
    }

    // الحصول على البيانات الأكاديمية بناءً على النوع
    public function academic()
    {
        return $this->user === 'طالب' 
            ? $this->student() 
            : $this->teacher();
    }
}