<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchDayProgress extends Model
{
    protected $table = 'batch_day_progress';
    
    protected $fillable = [
        'batch_id',
        'day_id',
        'is_completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'timestamp',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
