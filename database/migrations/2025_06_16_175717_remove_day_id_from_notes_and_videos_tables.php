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
        if (Schema::hasColumn('notes', 'day_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropForeign(['day_id']);
                $table->dropColumn('day_id');
            });
        }
        if (Schema::hasColumn('videos', 'day_id')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropForeign(['day_id']);
                $table->dropColumn('day_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not needed for this use case
    }
};
