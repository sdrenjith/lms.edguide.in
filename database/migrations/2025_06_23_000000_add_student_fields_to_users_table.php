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
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('dob')->nullable();
            $table->decimal('course_fee', 8, 2)->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('category')->nullable();
            $table->foreignId('batch_id')->nullable()->constrained()->onDelete('set null');
            $table->string('username')->unique()->nullable();
            $table->json('attachments')->nullable();
            $table->string('profile_picture')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'father_name',
                'mother_name',
                'dob',
                'course_fee',
                'phone',
                'gender',
                'nationality',
                'category',
                'batch_id',
                'username',
                'attachments',
                'profile_picture',
            ]);
        });
    }
}; 