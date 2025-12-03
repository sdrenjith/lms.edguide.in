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
            $table->string('qualification')->nullable()->after('category');
            $table->integer('experience_months')->nullable()->after('qualification');
            $table->text('address')->nullable()->after('experience_months');
            $table->string('passport_number')->nullable()->after('address');
            $table->decimal('fees_paid', 10, 2)->nullable()->after('passport_number');
            $table->decimal('balance_fees_due', 10, 2)->nullable()->after('fees_paid');
            $table->string('father_whatsapp')->nullable()->after('balance_fees_due');
            $table->string('mother_whatsapp')->nullable()->after('father_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'qualification',
                'experience_months',
                'address',
                'passport_number',
                'fees_paid',
                'balance_fees_due',
                'father_whatsapp',
                'mother_whatsapp',
            ]);
        });
    }
};
