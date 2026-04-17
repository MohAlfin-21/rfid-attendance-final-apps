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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('classroom_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->date('date');
            $table->string('status')->comment('present, late, excused, sick, absent');

            // Check-in fields
            $table->timestamp('check_in_at')->nullable();
            $table->string('check_in_method')->nullable()->comment('rfid, manual');
            $table->foreignId('check_in_device_id')
                ->nullable()
                ->constrained('devices')
                ->nullOnDelete();

            // Check-out fields
            $table->timestamp('check_out_at')->nullable();
            $table->string('check_out_method')->nullable()->comment('rfid, manual');
            $table->foreignId('check_out_device_id')
                ->nullable()
                ->constrained('devices')
                ->nullOnDelete();
            $table->string('check_out_type')->nullable()->comment('normal, early');
            $table->string('early_checkout_reason')->nullable();

            // Override fields (for manual corrections)
            $table->foreignId('override_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('override_note')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date'], 'attendance_user_date_unique');
            $table->index(['classroom_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
