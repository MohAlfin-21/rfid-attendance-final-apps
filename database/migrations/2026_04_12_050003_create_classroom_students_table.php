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
        Schema::create('classroom_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('academic_year')->comment('e.g. 2025/2026');
            $table->unsignedTinyInteger('semester')->comment('1 or 2');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['classroom_id', 'user_id', 'academic_year', 'semester'],
                'classroom_student_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_students');
    }
};
