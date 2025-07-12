<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'assignment_title',
        'files',
        'status',
        'remark',
        'mark',
    ];

    public function course() { 
        return $this->belongsTo(Course::class); 
    }

    public function student() { 
        return $this->belongsTo(User::class, 'student_id'); 
    }

    /**
     * Check if a specific assignment has been submitted by a student
     */
    public static function hasStudentSubmittedAssignment($courseId, $studentId, $assignmentTitle)
    {
        return self::where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->where('assignment_title', $assignmentTitle)
            ->exists();
    }
}
