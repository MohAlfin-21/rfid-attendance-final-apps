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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('action')->comment('check_in, check_out, manual_mark, early_checkout, rejected');
            $table->string('rfid_uid')->nullable()->comment('The scanned UID');
            $table->foreignId('device_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('rule_hit')->comment('ScanRuleHit enum value');
            $table->json('metadata')->nullable()->comment('Extra context: latency, firmware, etc.');
            $table->string('request_id')->nullable()->comment('Correlates with X-Request-Id header');
            $table->timestamps();

            $table->index('rfid_uid');
            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
