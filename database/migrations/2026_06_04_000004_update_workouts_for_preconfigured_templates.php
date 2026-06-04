<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workouts', function (Blueprint $table) {
            $table->text('workout_focus')->nullable()->after('workout_name');
            $table->integer('workout_reps')->nullable()->change();
            $table->integer('workout_sets')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('workouts')->whereNull('workout_reps')->update(['workout_reps' => 1]);
        DB::table('workouts')->whereNull('workout_sets')->update(['workout_sets' => 1]);

        Schema::table('workouts', function (Blueprint $table) {
            $table->dropColumn('workout_focus');
            $table->integer('workout_reps')->nullable(false)->change();
            $table->integer('workout_sets')->nullable(false)->change();
        });
    }
};
