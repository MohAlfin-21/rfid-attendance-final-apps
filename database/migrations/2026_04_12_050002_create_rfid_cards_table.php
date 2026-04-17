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
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique()->comment('Normalized RFID UID (uppercase, no separators)');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('status')->default('active')->comment('active, lost, inactive');
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('lost_at')->nullable();
            $table->timestamps();

            $table->index(['uid', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
