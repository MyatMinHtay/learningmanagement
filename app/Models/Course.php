<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'image',
        'description',
        'created_by',
        'duration',
        'category_id'
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_students', 'course_id', 'student_id')
                    ->withPivot('created_at');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasOne(Quiz::class);
    }

    public function coursequizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function assignments() { 
        return $this->hasMany(\App\Models\Assignment::class); 
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

}
