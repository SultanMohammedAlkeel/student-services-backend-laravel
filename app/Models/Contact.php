<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'friend_type',
        'is_blocked',
        'is_favorite',
    ];

    /**
     * Get the user that owns the contact
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the friend user
     */
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * Get the messages for this contact
     */

    /**
     * Get the folders this contact belongs to
     */
}
