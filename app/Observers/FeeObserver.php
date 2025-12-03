<?php

namespace App\Observers;

use App\Models\Fee;
use App\Models\User;

class FeeObserver
{
    /**
     * Handle the Fee "created" event.
     */
    public function created(Fee $fee): void
    {
        $this->updateStudentFees($fee->student_id);
    }

    /**
     * Handle the Fee "updated" event.
     */
    public function updated(Fee $fee): void
    {
        $this->updateStudentFees($fee->student_id);
    }

    /**
     * Handle the Fee "deleted" event.
     */
    public function deleted(Fee $fee): void
    {
        $this->updateStudentFees($fee->student_id);
    }

    /**
     * Handle the Fee "restored" event.
     */
    public function restored(Fee $fee): void
    {
        $this->updateStudentFees($fee->student_id);
    }

    /**
     * Handle the Fee "force deleted" event.
     */
    public function forceDeleted(Fee $fee): void
    {
        $this->updateStudentFees($fee->student_id);
    }

    /**
     * Update student fees calculation
     */
    private function updateStudentFees(int $studentId): void
    {
        $student = User::find($studentId);
        
        if ($student && $student->role === 'student') {
            // Calculate actual fees paid from Fee model
            $actualFeesPaid = $student->fees()->sum('amount_paid');
            
            // Calculate balance
            $balance = $student->course_fee - $actualFeesPaid;
            
            // Update the student record
            $student->update([
                'fees_paid' => $actualFeesPaid,
                'balance_fees_due' => $balance,
            ]);
        }
    }
}
