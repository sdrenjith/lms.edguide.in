<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\Day;

class BatchDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all days and batches
        $days = Day::all();
        $batches = Batch::all();

        if ($days->isEmpty() || $batches->isEmpty()) {
            $this->command->info('No days or batches found. Please create some first.');
            return;
        }

        // Example assignments - you can customize these
        foreach ($batches as $batch) {
            // Assign first 5 days to each batch as an example
            $assignedDays = $days->take(5);
            $batch->days()->sync($assignedDays->pluck('id'));
            
            $this->command->info("Assigned days [{$assignedDays->pluck('title')->implode(', ')}] to batch: {$batch->name}");
        }

        $this->command->info('Batch day assignments completed!');
    }
}
