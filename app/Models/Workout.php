<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['program_id', 'workout_name', 'workout_focus', 'workout_sets', 'reps_min', 'reps_max', 'workout_day', 'position'])]
class Workout extends Model
{
    protected function casts(): array
    {
        return [
            'workout_sets' => 'integer',
            'reps_min' => 'integer',
            'reps_max' => 'integer',
            'position' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
