<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('webhook_endpoints')) {
            Schema::create('webhook_endpoints', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->string('name');
                $table->string('url');
                $table->json('events')->nullable();
                $table->boolean('active')->default(true);
                $table->string('secret')->nullable();
                $table->dateTime('last_used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};

