<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('plan_id');
                $table->string('status')->default('trial');
                $table->dateTime('trial_ends_at')->nullable();
                $table->dateTime('current_period_ends_at')->nullable();
                $table->dateTime('canceled_at')->nullable();
                $table->dateTime('suspended_at')->nullable();
                $table->timestamps();
                $table->index('tenant_id');
                $table->index('plan_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

