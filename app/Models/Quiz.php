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
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }
}
