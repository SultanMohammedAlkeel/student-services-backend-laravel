<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'content',
        'file_url',
        'file_type',
        'file_size',
        'views_count',
        'likes_count',
        'comments_count',
        'deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }
}
