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
        // Make day_number nullable in videos table
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('day_number')->nullable()->change();
        });
        
        // Make day_number nullable in notes table
        Schema::table('notes', function (Blueprint $table) {
            $table->integer('day_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert day_number to NOT NULL in videos table
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('day_number')->nullable(false)->change();
        });
        
        // Revert day_number to NOT NULL in notes table
        Schema::table('notes', function (Blueprint $table) {
            $table->integer('day_number')->nullable(false)->change();
        });
    }
};
