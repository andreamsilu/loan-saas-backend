<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('api_usage_logs')) {
            Schema::create('api_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('api_key_id')->nullable()->index();
                $table->string('method', 10);
                $table->string('path');
                $table->unsignedSmallInteger('status');
                $table->unsignedInteger('duration_ms')->nullable();
                $table->string('ip', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->dateTime('occurred_at');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};

