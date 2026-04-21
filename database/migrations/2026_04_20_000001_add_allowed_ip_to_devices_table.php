<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds `allowed_ip` column to devices table for IP whitelist security.
     * If null, any IP is allowed (backward-compatible default).
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('allowed_ip', 45)
                ->nullable()
                ->after('is_active')
                ->comment('If set, only requests from this IP are accepted (supports single IP or CIDR). Null = any IP allowed.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('allowed_ip');
        });
    }
};
