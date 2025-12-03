<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'gmeet_link',
        'description',
        'session_date',
        'session_time',
        'is_active',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
} 