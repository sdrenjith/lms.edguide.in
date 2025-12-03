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
        // First, update any existing notes that have NULL pdf_path to have a default value
        // This is a safety measure before making the column NOT NULL
        \DB::table('notes')->whereNull('pdf_path')->update(['pdf_path' => '']);
        
        // Make pdf_path NOT NULL
        Schema::table('notes', function (Blueprint $table) {
            $table->string('pdf_path')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert pdf_path to nullable
        Schema::table('notes', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->change();
        });
    }
};
