<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'workout_name', 'workout_focus', 'workout_reps', 'workout_sets', 'workout_day'])]
class Workout extends Model
{
    protected function casts(): array
    {
        return [
            'workout_reps' => 'integer',
            'workout_sets' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
