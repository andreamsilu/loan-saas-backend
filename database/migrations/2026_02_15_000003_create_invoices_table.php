<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('subscription_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->string('status')->default('unpaid');
                $table->date('due_date')->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index('tenant_id');
                $table->index('subscription_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

