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
            // Add guardian name field (general guardian, not just father/mother)
            $table->string('guardian_name')->nullable()->after('mother_name');
            
            // Add financial information fields
            $table->decimal('total_course_fee', 10, 2)->nullable()->after('course_fee');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('total_course_fee');
            $table->string('payment_method')->nullable()->after('discount_amount');
            $table->text('financial_notes')->nullable()->after('payment_method');
            
            // Add address information fields (enhance existing address field)
            $table->string('street_address')->nullable()->after('address');
            $table->string('city')->nullable()->after('street_address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'guardian_name',
                'total_course_fee',
                'discount_amount',
                'payment_method',
                'financial_notes',
                'street_address',
                'city',
                'state',
                'postal_code',
                'country',
            ]);
        });
    }
};