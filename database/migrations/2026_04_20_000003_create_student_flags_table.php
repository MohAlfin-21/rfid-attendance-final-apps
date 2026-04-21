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
        Schema::create('student_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('flag_type')->comment('late_pattern | consecutive_absent | fast_checkout');
            $table->jsonb('details')->nullable()->comment('Structured details about the anomaly');
            $table->date('flagged_date')->comment('The reference date for this flag');
            $table->timestamp('resolved_at')->nullable()->comment('When an admin marked this as resolved');
            $table->timestamps();

            $table->index(['user_id', 'flag_type']);
            $table->index(['resolved_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_flags');
    }
};
