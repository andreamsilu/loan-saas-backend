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
        Schema::table('transactions', function (Blueprint $table) {
            // Check if columns exist before adding them, in case the previous migration failed halfway
            if (!Schema::hasColumn('transactions', 'transaction_number')) {
                $table->string('transaction_number')->unique()->after('loan_id');
                $table->string('payment_method')->nullable()->after('type');
                $table->string('reference')->nullable()->after('payment_method');
                $table->json('metadata')->nullable()->after('reference');
                $table->timestamp('transaction_date')->after('metadata');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_number', 'payment_method', 'reference', 'metadata', 'transaction_date']);
        });
    }
};
