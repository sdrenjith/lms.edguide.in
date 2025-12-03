<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Fee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateStudentFeesCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:update-fees-calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update student fees calculation to use dynamic calculations from Fee model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting student fees calculation update...');

        // Get all students
        $students = User::where('role', 'student')->get();
        
        $this->info("Found {$students->count()} students to process.");

        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        foreach ($students as $student) {
            // Calculate actual fees paid from Fee model
            $actualFeesPaid = $student->fees()->sum('amount_paid');
            
            // Calculate balance
            $balance = $student->course_fee - $actualFeesPaid;
            
            // Update the student record with calculated values
            $student->update([
                'fees_paid' => $actualFeesPaid,
                'balance_fees_due' => $balance,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Show summary
        $totalStudents = $students->count();
        $totalCourseFees = $students->sum('course_fee');
        $totalFeesPaid = Fee::sum('amount_paid');
        $totalBalance = $totalCourseFees - $totalFeesPaid;

        $this->info('Fees calculation update completed!');
        $this->info("Total Students: {$totalStudents}");
        $this->info("Total Course Fees: ₹" . number_format($totalCourseFees, 2));
        $this->info("Total Fees Paid: ₹" . number_format($totalFeesPaid, 2));
        $this->info("Total Balance: ₹" . number_format($totalBalance, 2));

        return Command::SUCCESS;
    }
}
