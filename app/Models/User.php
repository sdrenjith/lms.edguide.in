<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'guardian_name',
        'dob',
        'age',
        'course_fee',
        'phone',
        'gender',
        'nationality',
        'batch_id',
        'qualification',
        'address',
        'total_score',
        // Keep existing fields for backward compatibility
        'father_name',
        'mother_name',
        'category',
        'username',
        'attachments',
        'profile_picture',
        'experience_months',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'passport_number',
        'fees_paid',
        'balance_fees_due',
        'father_whatsapp',
        'mother_whatsapp',
        'total_course_fee',
        'discount_amount',
        'payment_method',
        'financial_notes',
        'is_verified',
        'verification_code',
        'verified_at',
        'verification_code_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'attachments' => 'array',
            'dob' => 'date',
            'age' => 'integer',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    /**
     * Check if the user is a datamanager.
     */
    public function isDataManager(): bool
    {
        return strtolower($this->role) === 'datamanager';
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return strtolower($this->role) === 'teacher';
    }

    /**
     * Check if the user is an accounts user.
     */
    public function isAccounts(): bool
    {
        return strtolower($this->role) === 'accounts';
    }

    /**
     * Check if the user is a dataentry user.
     */
    public function isDataEntry(): bool
    {
        return strtolower($this->role) === 'dataentry';
    }

    /**
     * Check if the user is a manager.
     */
    public function isManager(): bool
    {
        return strtolower($this->role) === 'manager';
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function assignedCourses()
    {
        // If user is verified and has a verification code that is not expired, get courses from verification code
        if ($this->is_verified && $this->verificationCode && !$this->verificationCode->isExpired()) {
            return collect([$this->verificationCode->course]);
        }
        
        // Fallback to batch courses
        return $this->batch ? $this->batch->courses : collect();
    }

    public function assignedDays()
    {
        // If user is verified and has a verification code that is not expired, get days from verification code's course
        if ($this->is_verified && $this->verificationCode && !$this->verificationCode->isExpired()) {
            return $this->verificationCode->course->days ?? collect();
        }
        
        // Fallback to batch days
        return $this->batch ? $this->batch->days : collect();
    }

    public function assignedSubjects()
    {
        // If user is verified and has a verification code that is not expired, get subject from verification code
        if ($this->is_verified && $this->verificationCode && !$this->verificationCode->isExpired()) {
            return collect([$this->verificationCode->subject]);
        }
        
        // Fallback to batch subjects (if any)
        return $this->batch ? $this->batch->subjects : collect();
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->withTimestamps();
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'teacher_id');
    }

    public function fees()
    {
        return $this->hasMany(Fee::class, 'student_id');
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class);
    }

    public function verificationCode()
    {
        return $this->belongsTo(VerificationCode::class);
    }

    public function currentActivity()
    {
        return $this->hasOne(StudentActivity::class)->whereNull('logout_at')->latest();
    }

    /**
     * Get the total fees paid by this student (calculated dynamically)
     */
    public function getTotalFeesPaidAttribute(): float
    {
        return $this->fees()->sum('amount_paid');
    }

    /**
     * Get the balance fees due (calculated dynamically)
     */
    public function getBalanceFeesDueAttribute(): float
    {
        return $this->course_fee - $this->total_fees_paid;
    }

    /**
     * Check if student has paid all fees
     */
    public function getHasPaidAllFeesAttribute(): bool
    {
        return $this->balance_fees_due <= 0;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->course_fee <= 0) {
            return 0;
        }
        return round(($this->total_fees_paid / $this->course_fee) * 100, 1);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin() || $this->isTeacher() || $this->isAccounts() || $this->isDataEntry() || $this->isManager();
        }

        if ($panel->getId() === 'student') {
            return strtolower($this->role) === 'student';
        }

        return false;
    }
}
