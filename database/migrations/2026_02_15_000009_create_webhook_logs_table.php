<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('webhook_logs')) {
            Schema::create('webhook_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->unsignedBigInteger('webhook_endpoint_id')->index();
                $table->string('event');
                $table->json('payload')->nullable();
                $table->unsignedSmallInteger('response_status')->nullable();
                $table->text('response_body')->nullable();
                $table->unsignedInteger('attempts')->default(1);
                $table->dateTime('last_attempt_at')->nullable();
                $table->boolean('success')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};

