<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'created_by',
        'duration'
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_students', 'course_id', 'student_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    

}
