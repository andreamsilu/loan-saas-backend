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
        Schema::table('borrowers', function (Blueprint $table) {
            $table->string('id_number')->after('last_name');
            $table->string('status')->default('active')->after('phone'); // active, blacklisted
            $table->json('metadata')->nullable()->after('status');
            $table->unique(['tenant_id', 'id_number']);
            $table->unique(['tenant_id', 'email']);
            $table->unique(['tenant_id', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'id_number']);
            $table->dropUnique(['tenant_id', 'email']);
            $table->dropUnique(['tenant_id', 'phone']);
            $table->dropColumn(['id_number', 'status', 'metadata']);
        });
    }
};
