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

}
