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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Human-readable device identifier');
            $table->string('name');
            $table->string('location')->nullable()->comment('Physical location description');
            $table->string('token_hash', 64)->unique()->comment('SHA-256 hash of the plain token');
            $table->text('token_plain_encrypted')->comment('Encrypted plain token for admin display');
            $table->boolean('is_active')->default(true);
            $table->string('firmware_version')->nullable();
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->unsignedInteger('heartbeat_interval_seconds')->default(60);
            $table->timestamp('last_error_at')->nullable();
            $table->string('last_error_message')->nullable();
            $table->unsignedInteger('error_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
