<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batch;

class CleanupBatchProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:cleanup-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up incorrect progress records for batch days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up incorrect batch progress records...');
        
        $totalRemoved = 0;
        $batches = Batch::all();
        
        foreach ($batches as $batch) {
            // Get assigned day IDs for this batch
            $assignedDayIds = $batch->days->pluck('id')->toArray();
            
            // Remove progress records for days that are not assigned to this batch
            $removedCount = $batch->dayProgress()
                ->whereNotIn('day_id', $assignedDayIds)
                ->delete();
            
            if ($removedCount > 0) {
                $this->info("Batch '{$batch->name}': Removed {$removedCount} incorrect progress records");
                $totalRemoved += $removedCount;
            }
        }
        
        $this->info("Total incorrect progress records removed: {$totalRemoved}");
        $this->info('Cleanup completed successfully!');
        
        return 0;
    }
}
