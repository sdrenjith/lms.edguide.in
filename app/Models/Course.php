<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name'];

    public function days()
    {
        return $this->hasMany(Day::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subjects()
    {
        return \App\Models\Subject::all();
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_course');
    }
}
