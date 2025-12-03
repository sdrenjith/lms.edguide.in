<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionType extends Model
{
    protected $fillable = ['name'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get user-friendly display name for the question type
     */
    public function getDisplayNameAttribute()
    {
        $displayNames = [
            'mcq_single' => 'MCQ single answer',
            'mcq_multiple' => 'MCQ multiple answer',
            'reorder' => 'Rearrange options',
            'opinion' => 'Essay/para writing',
            'statement_match' => 'Match the following',
            'true_false' => 'True or false- single question',
            'true_false_multiple' => 'True or false multiple questions',
            'form_fill' => 'Fill in the blanks',
            'audio_mcq_single' => 'Audio with MCQ',
            'audio_image_text_single' => 'Audio with image matching',
            'audio_image_text_multiple' => 'Multiple audio text matching',
            'picture_mcq' => 'Image to text matching',
            'audio_fill_blank' => 'Audio fill in the blanks',
            'picture_fill_blank' => 'Picture fill in the blanks',
            'video_fill_blank' => 'Video fill in the blanks',
            'audio_picture_match' => 'Audio + image matching',
        ];

        return $displayNames[$this->name] ?? $this->name;
    }
} 