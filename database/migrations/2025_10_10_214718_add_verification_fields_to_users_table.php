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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->string('verification_code')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verification_code_id')->nullable();
            
            $table->foreign('verification_code_id')->references('id')->on('verification_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['verification_code_id']);
            $table->dropColumn(['is_verified', 'verification_code', 'verified_at', 'verification_code_id']);
        });
    }
};
