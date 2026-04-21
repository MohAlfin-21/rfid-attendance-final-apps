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
        Schema::create('student_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('current_streak')->default(0)
                ->comment('Consecutive school days with on-time attendance');
            $table->unsignedInteger('longest_streak')->default(0)
                ->comment('All-time longest streak');
            $table->unsignedInteger('total_points')->default(0)
                ->comment('Cumulative gamification points');
            $table->date('last_streak_date')->nullable()
                ->comment('Date the streak was last incremented');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_streaks');
    }
};
