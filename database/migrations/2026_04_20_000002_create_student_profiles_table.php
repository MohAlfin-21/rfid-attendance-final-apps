<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Separate profile table for student-specific data.
     * Keeps the `users` table lean and avoids null columns for non-student roles.
     */
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('parent_phone', 20)
                ->nullable()
                ->comment('Phone number of the student\'s parent/guardian, used for WA notifications.');
            $table->string('parent_name', 100)
                ->nullable()
                ->comment('Name of the parent/guardian for notification personalization.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
