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
        // Assign default course (A1 - ID 1) to existing subjects that don't have a course_id
        DB::table('subjects')
            ->whereNull('course_id')
            ->update(['course_id' => 1]); // A1 course
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as we're setting default values
    }
};
