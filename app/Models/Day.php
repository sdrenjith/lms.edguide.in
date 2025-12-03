<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable = ['name', 'number', 'course_id', 'title' , 'status',];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_day');
    }

    public function courses()
    {
        // For compatibility with views expecting a collection
        return $this->course() ? $this->course()->get() : collect();
    }

    protected static function booted()
    {
        static::saving(function ($day) {
            if ($day->day_number) {
                $day->title = 'Day ' . $day->day_number;
            }
        });
    }

    public function getTitleWithCourseAttribute()
    {
        return $this->course ? "{$this->course->name} - {$this->title}" : $this->title;
    }
}
