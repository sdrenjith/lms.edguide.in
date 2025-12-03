<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\DB;

class ClearStudentAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:clear-answers {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all student answers from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $totalAnswers = StudentAnswer::count();
        
        if ($totalAnswers === 0) {
            $this->info('No student answers found in the database.');
            return 0;
        }

        $this->warn("⚠️  WARNING: This will permanently delete ALL student answers!");
        $this->line("Found {$totalAnswers} student answer(s) to delete.");
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to proceed? This action cannot be undone.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting to clear student answers...');
        
        try {
            DB::beginTransaction();
            
            // Delete all student answers
            $deletedCount = StudentAnswer::query()->delete();
            
            DB::commit();
            
            $this->info("✅ Successfully deleted {$deletedCount} student answer(s).");
            $this->info('All student progress has been reset.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred while clearing student answers: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 