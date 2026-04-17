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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('e.g. XII-RPL-1');
            $table->string('name')->comment('e.g. XII RPL 1');
            $table->unsignedTinyInteger('grade')->comment('10, 11, or 12');
            $table->string('major')->nullable()->comment('e.g. RPL, TKJ, etc.');
            $table->foreignId('homeroom_teacher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
