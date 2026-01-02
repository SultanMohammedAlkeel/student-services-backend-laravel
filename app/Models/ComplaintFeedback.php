<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintFeedback extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'complaints_feedback';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the complaint/feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include complaints.
     */
    public function scopeComplaints($query)
    {
        return $query->where('type', 'complaint');
    }

    /**
     * Scope a query to only include feedback.
     */
    public function scopeFeedback($query)
    {
        return $query->where('type', 'feedback');
    }

    /**
     * Scope a query to only include new items.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope a query to only include read items.
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope a query to only include archived items.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Get the formatted type attribute.
     */
    public function getFormattedTypeAttribute(): string
    {
        return $this->type === 'complaint' ? 'شكوى' : 'ملاحظة';
    }

    /**
     * Get the formatted status attribute.
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'new' => 'جديد',
            'read' => 'مقروء',
            'archived' => 'مؤرشف',
            default => 'غير محدد'
        };
    }
}

