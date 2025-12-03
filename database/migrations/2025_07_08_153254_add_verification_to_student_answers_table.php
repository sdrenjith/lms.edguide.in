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
        Schema::table('student_answers', function (Blueprint $table) {
            $table->enum('verification_status', ['pending', 'verified_correct', 'verified_incorrect'])
                  ->default('pending')
                  ->after('is_correct')
                  ->comment('For opinion questions: pending, verified_correct, verified_incorrect');
            
            $table->unsignedBigInteger('verified_by')->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('verification_comment')->nullable()->after('verified_at');
            
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'verification_comment']);
        });
    }
};
