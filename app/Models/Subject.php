<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'course_id'];

    public function course()
    {
        return $this->belongsTo(\App\Models\Course::class);
    }

    public function days()
    {
        return $this->hasMany(Day::class, 'course_id', 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Question::class);
    }

    // Many-to-many relationship with teachers
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'teacher_id')
                    ->where('role', 'teacher')
                    ->withTimestamps();
    }

    // Helper method to get teacher names as a string
    public function getTeacherNamesAttribute()
    {
        return $this->teachers->pluck('name')->implode(', ');
    }
}
