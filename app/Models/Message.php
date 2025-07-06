<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'course_id',
        'message',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeForConversation($query, $userId1, $userId2, $courseId)
    {
        return $query->where('course_id', $courseId)
            ->where(function ($q) use ($userId1, $userId2) {
                $q->where(function ($subQ) use ($userId1, $userId2) {
                    $subQ->where('sender_id', $userId1)
                         ->where('receiver_id', $userId2);
                })->orWhere(function ($subQ) use ($userId1, $userId2) {
                    $subQ->where('sender_id', $userId2)
                         ->where('receiver_id', $userId1);
                });
            });
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
