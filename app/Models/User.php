<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'email',
    'password',
    'first_name',
    'last_name',
    'birthdate',
    'height',
    'height_value',
    'height_unit',
    'weight',
    'weight_value',
    'weight_unit',
    'activity',
    'sex',
    'google_id',
    'microsoft_id',
    'avatar',
    'provider',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function workouts(): HasManyThrough
    {
        return $this->hasManyThrough(Workout::class, Program::class);
    }
}
