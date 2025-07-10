<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'is_completed',
        'started_at',
        'ended_at',
        'grade',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Calculate marks earned for this attempt
     */
    public function calculateMarksEarned()
    {
        $marksEarned = 0;
        
        foreach ($this->answers as $answer) {
            if ($answer->choice && $answer->choice->is_correct) {
                $marksEarned += $answer->question->marks;
            }
        }
        
        return $marksEarned;
    }

    /**
     * Get percentage score based on marks
     */
    public function getPercentageAttribute()
    {
        $totalMarks = $this->quiz->calculateTotalMarks();
        $marksEarned = $this->score; // This will be the marks earned after we fix the controller
        
        return $totalMarks > 0 ? ($marksEarned / $totalMarks) * 100 : 0;
    }

    /**
     * Get grade based on percentage
     */
    public function getGradeStatusAttribute()
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 80) {
            return 'Excellent';
        } elseif ($percentage >= 60) {
            return 'Good';
        } elseif ($percentage >= 50) {
            return 'Normal';
        } else {
            return 'Poor';
        }
    }
}
