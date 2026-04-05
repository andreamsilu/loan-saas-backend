<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Removes SaaS platform tables (plans, subscriptions, developer portal, webhooks).
 * Fresh installs no longer create these; this migration cleans existing databases.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhook_endpoints');
        Schema::dropIfExists('api_usage_logs');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('global_settings');
    }

    public function down(): void
    {
        // Irreversible: SaaS schema is no longer part of this application.
    }
};
