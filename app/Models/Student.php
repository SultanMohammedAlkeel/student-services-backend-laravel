<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
     //
    
    protected $fillable = [
        'full_name', // أضف هذا الحقل
        'email',
        'phone_number',
        'gender',
        'address',
        'date_of_birth',
        'enrollment_year',
        'department_id',
        'level',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    // public function activationCodes()
    // {
    //     return $this->hasMany(ActivationCode::class, 'account_id');
    // }
    public function user()
    {
        return $this->hasMany(User::class, 'user_id'); // تأكد من أن المفتاح الخارجي صحيح
    }
    public function user_acount()
    {
        // تغيير من hasMany إلى belongsTo
        return $this->belongsTo(User::class, 'user_id'); // تأكد من وجود user_id في جدول students
    }
    public function contactInfos()
    {
        return $this->hasMany(ContactInfo::class);
    }

}
