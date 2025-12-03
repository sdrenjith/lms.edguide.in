<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\Course;

class BatchCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all courses and batches
        $courses = Course::all();
        $batches = Batch::all();

        if ($courses->isEmpty() || $batches->isEmpty()) {
            $this->command->info('No courses or batches found. Please create some first.');
            return;
        }

        // Example assignments - you can customize these
        foreach ($batches as $batch) {
            // Assign first 2 courses to each batch as an example
            $assignedCourses = $courses->take(2);
            $batch->courses()->sync($assignedCourses->pluck('id'));
            
            $this->command->info("Assigned courses [{$assignedCourses->pluck('name')->implode(', ')}] to batch: {$batch->name}");
        }

        $this->command->info('Batch course assignments completed!');
    }
}
