<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'last_activity_at',
        'ip_address',
        'user_agent',
        'logout_type',
        'session_duration_minutes',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate session duration when logout occurs
     */
    public function calculateSessionDuration()
    {
        if ($this->logout_at && $this->login_at) {
            $this->session_duration_minutes = abs($this->logout_at->diffInMinutes($this->login_at));
            $this->save();
        }
    }

    /**
     * Check if session is idle (no activity for specified minutes)
     */
    public function isIdle($minutes = 30)
    {
        if (!$this->last_activity_at) {
            return true;
        }
        
        return $this->last_activity_at->diffInMinutes(now()) >= $minutes;
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity()
    {
        $this->last_activity_at = now();
        $this->save();
    }

    /**
     * Scope for active sessions (logged in but not logged out)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logout_at');
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }
}