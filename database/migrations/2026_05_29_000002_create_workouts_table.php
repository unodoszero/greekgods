<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('workout_name');
            $table->integer('workout_reps');
            $table->integer('workout_sets');
            $table->text('workout_day');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'workout_day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
