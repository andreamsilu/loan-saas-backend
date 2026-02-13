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
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('interest_calculation_type'); // flat, reducing_balance
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_duration');
            $table->string('term_period'); // days, weeks, months
            $table->decimal('min_amount', 15, 2);
            $table->decimal('max_amount', 15, 2);
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->string('processing_fee_type')->default('fixed'); // fixed, percentage
            $table->integer('grace_period_days')->default(0);
            $table->string('repayment_frequency'); // daily, weekly, monthly
            $table->decimal('penalty_rate', 5, 2)->default(0);
            $table->string('penalty_type')->default('percentage'); // fixed, percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
