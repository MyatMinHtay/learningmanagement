<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'created_by',
        'total_marks',
        'total_questions',
        'total_time',
        'is_time_limited',
        'grade',
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate total marks available for this quiz
     */
    public function getTotalMarksAttribute()
    {
        return $this->questions->sum('marks');
    }

    /**
     * Calculate total possible marks for this quiz
     */
    public function calculateTotalMarks()
    {
        return $this->questions()->sum('marks');
    }

    /**
     * Get the total questions count
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->questions->count();
    }

}
