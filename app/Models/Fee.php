<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'batch_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fee) {
            // Automatically set batch_id from student if not provided
            if (!$fee->batch_id && $fee->student_id) {
                $student = User::find($fee->student_id);
                if ($student && $student->batch_id) {
                    $fee->batch_id = $student->batch_id;
                }
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function getStudentNameAttribute(): string
    {
        return $this->student ? $this->student->name : 'Unknown Student';
    }

    public function getBatchNameAttribute(): string
    {
        return $this->batch ? $this->batch->name : 'No Batch';
    }
}
