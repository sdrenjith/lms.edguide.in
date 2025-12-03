<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'answer_data',
        'file_upload',
        'is_correct',
        'submitted_at',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_comment',
    ];

    protected $casts = [
        'answer_data' => 'array',
        'is_correct' => 'boolean',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isOpinionQuestion()
    {
        return $this->question && $this->question->questionType && $this->question->questionType->name === 'opinion';
    }

    public function needsVerification()
    {
        return $this->isOpinionQuestion() && $this->verification_status === 'pending';
    }

    public function isVerified()
    {
        return $this->verification_status !== 'pending';
    }
} 