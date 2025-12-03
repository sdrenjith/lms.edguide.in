<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VerificationCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'course_id',
        'subject_id',
        'is_used',
        'used_by_user_id',
        'used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'is_active' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($verificationCode) {
            if (empty($verificationCode->code)) {
                $verificationCode->code = strtoupper(Str::random(8));
            }
            
            // Set default expiry date to 4 months from now if not provided
            if (empty($verificationCode->expires_at)) {
                $verificationCode->expires_at = now()->addMonths(4);
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }


    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'verification_code_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeUsed(): bool
    {
        return $this->is_active && !$this->is_used && !$this->isExpired();
    }

    public function markAsUsed(User $user): void
    {
        $this->update([
            'is_used' => true,
            'used_by_user_id' => $user->id,
            'used_at' => now(),
        ]);
    }
}
