<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('workouts')->delete();
        DB::table('programs')->delete();

        Schema::dropIfExists('workouts');

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->unique('user_id');
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('workout_day', 16);
            $table->string('workout_name', 120);
            $table->string('workout_focus', 120)->nullable();
            $table->unsignedSmallInteger('workout_sets');
            $table->unsignedSmallInteger('reps_min');
            $table->unsignedSmallInteger('reps_max');
            $table->unsignedSmallInteger('position');
            $table->timestamps();

            $table->unique(['program_id', 'position']);
            $table->index(['program_id', 'workout_day', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workouts');

        Schema::table('programs', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->index('user_id');
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('workout_name');
            $table->text('workout_focus')->nullable();
            $table->integer('workout_reps')->nullable();
            $table->integer('workout_sets')->nullable();
            $table->text('workout_day');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'workout_day']);
        });
    }
};
