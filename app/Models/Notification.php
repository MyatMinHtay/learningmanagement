<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'recipient_id',
        'sender_id',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('recipient_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static methods for creating notifications
    public static function createEnrollmentNotification($courseCreatorId, $studentId, $courseId, $courseName)
    {

        $student = User::find($studentId);
        $studentName = $student ? $student->username : 'A student';

        return self::create([
            'type' => 'enrollment',
            'recipient_id' => $courseCreatorId,
            'sender_id' => $studentId,
            'title' => 'New Course Enrollment',
            'message' => "{$studentName} has enrolled in your course: {$courseName}",
            'data' => [
                'course_id' => $courseId,
                'student_id' => $studentId
            ]
        ]);
    }

    public static function createAssignmentSubmissionNotification($courseCreatorId, $studentId, $assignmentId, $courseName)
    {
        $student = User::find($studentId);
        $studentName = $student ? $student->username : 'A student';
        
        return self::create([
            'type' => 'assignment_submitted',
            'recipient_id' => $courseCreatorId,
            'sender_id' => $studentId,
            'title' => 'Assignment Submitted',
            'message' => "{$studentName} has submitted an assignment for course: {$courseName}",
            'data' => [
                'assignment_id' => $assignmentId,
                'student_id' => $studentId
            ]
        ]);
    }

    public static function createDeadlineNotification($recipientIds, $senderId, $title, $message, $data = [])
    {
        $notifications = [];
        $deadlineDate = isset($data['deadline']) ? $data['deadline'] : null;
        
        // Add deadline date to message if available
        if ($deadlineDate) {
            $formattedDate = date('F j, Y', strtotime($deadlineDate));
            $message .= " (Due date: {$formattedDate})";
        }

        foreach ($recipientIds as $recipientId) {
            $notifications[] = [
                'type' => 'deadline_reminder',
                'recipient_id' => $recipientId,
                'sender_id' => $senderId,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        return self::insert($notifications);
    }
}
