<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'day_id',
        'course_id',
        'subject_id',
        'test_id',
        'topic',
        'question_type_id',
        'instruction',
        'question_data',
        'answer_data',
        'explanation',
        'points',
        'left_options',
        'right_options',
        'correct_pairs',
        'is_active',
        'audio_image_text_images',
        'audio_image_text_audio_file',
        'picture_mcq_images',
        'audio_image_text_multiple_pairs',
        'true_false_questions',
        'form_fill_paragraph',
        'reorder_fragments',
        'reorder_answer_key',
    ];

    protected $casts = [
        'question_data' => 'array',
        'answer_data' => 'array',
        'left_options' => 'array',
        'right_options' => 'array',
        'correct_pairs' => 'array',
        'audio_image_text_images' => 'array',
        'picture_mcq_images' => 'array',
        'audio_image_text_multiple_pairs' => 'array',
        'true_false_questions' => 'array',
        'reorder_fragments' => 'array',
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class);
    }

    public function media()
    {
        return $this->hasMany(QuestionMedia::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}