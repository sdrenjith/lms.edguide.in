<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['active_day_id']);
            // Then drop the column
            $table->dropColumn('active_day_id');
            // Add new JSON column for multiple active days
            $table->json('active_day_ids')->nullable()->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Revert back to single active_day_id
            $table->dropColumn('active_day_ids');
            $table->unsignedBigInteger('active_day_id')->nullable()->after('teacher_id');
            // Re-add foreign key constraint
            $table->foreign('active_day_id')->references('id')->on('days')->onDelete('set null');
        });
    }
};
