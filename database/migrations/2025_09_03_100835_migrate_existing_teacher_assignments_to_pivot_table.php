<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all subjects with existing teacher assignments
        $subjectsWithTeachers = DB::table('subjects')
            ->whereNotNull('teacher_id')
            ->where('teacher_id', '!=', '')
            ->get();

        foreach ($subjectsWithTeachers as $subject) {
            // Insert into the pivot table
            DB::table('subject_teacher')->insert([
                'subject_id' => $subject->id,
                'teacher_id' => $subject->teacher_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we're moving data
        // The data will remain in the pivot table
    }
};
