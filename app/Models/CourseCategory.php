<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'sort_order',
    ];

    /**
     * Get all courses that belong to this category.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Get categories ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get course count for this category.
     */
    public function getCourseCountAttribute()
    {
        return $this->courses()->count();
    }
} 