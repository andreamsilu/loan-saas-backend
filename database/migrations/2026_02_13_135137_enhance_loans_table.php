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
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('loan_product_id')->after('tenant_id')->constrained();
            $table->string('loan_number')->unique()->after('loan_product_id');
            $table->date('application_date')->after('status');
            $table->date('approval_date')->nullable()->after('application_date');
            $table->date('disbursement_date')->nullable()->after('approval_date');
            $table->date('maturity_date')->nullable()->after('disbursement_date');
            $table->decimal('total_payable', 15, 2)->default(0)->after('amount');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_payable');
            $table->json('repayment_schedule')->nullable()->after('total_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['loan_product_id']);
            $table->dropColumn([
                'loan_product_id', 'loan_number', 'application_date', 
                'approval_date', 'disbursement_date', 'maturity_date', 
                'total_payable', 'total_paid', 'repayment_schedule'
            ]);
        });
    }
};
