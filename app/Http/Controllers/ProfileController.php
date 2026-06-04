<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileAccountRequest;
use App\Http\Requests\UpdateProfileBodyMetricsRequest;
use App\Http\Requests\UpdateProfilePasswordRequest;
use App\Support\AuthSessionIdentity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * @var array<string, float>
     */
    private const ACTIVITY_FACTORS = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'very_active' => 1.9,
    ];

    /**
     * @var array<string, string>
     */
    private const ACTIVITY_LABELS = [
        'sedentary' => 'Sedentary',
        'light' => 'Light',
        'moderate' => 'Moderate',
        'active' => 'Active',
        'very_active' => 'Very active',
    ];

    /**
     * @var array<string, string>
     */
    private const SEX_LABELS = [
        'male' => 'Male',
        'female' => 'Female',
        'prefer_not_to_say' => 'Prefer not to say',
    ];

    public function show(Request $request): View
    {
        $user = Auth::user();
        AuthSessionIdentity::store($request, $user);
        $today = now()->format('l');
        $workouts = $user->workouts()
            ->where('workout_day', $today)
            ->orderBy('id')
            ->get(['workout_name as workoutName', 'workout_focus as workoutFocus', 'workout_reps as workoutReps', 'workout_sets as workoutSets']);
        $profile = $this->profilePayload($user);

        return view('profile.show', [
            'user' => $user,
            'workouts' => $workouts,
            'profile' => $profile,
            'userData' => $profile['user'],
        ]);
    }

    public function data(): JsonResponse
    {
        $profile = $this->profilePayload(Auth::user());

        return response()->json([
            'success' => true,
            'user' => $profile['user'],
            'metrics' => $profile['metrics'],
        ]);
    }

    public function updateAccount(UpdateProfileAccountRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
        ]);
        $user->save();
        AuthSessionIdentity::store($request, $user);

        return $this->profileJson($user, 'Account details updated.');
    }

    public function updateBodyMetrics(UpdateProfileBodyMetricsRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->fill([
            'birthdate' => $data['birthdate'],
            'height' => round((float) $request->input('height'), 5),
            'height_value' => $data['height_value'],
            'height_unit' => $data['height_unit'],
            'weight' => round((float) $request->input('weight'), 2),
            'weight_value' => $data['weight_value'],
            'weight_unit' => $data['weight_unit'],
            'activity' => $data['activity'],
            'sex' => $data['sex'] ?? null,
        ]);
        $user->save();

        return $this->profileJson($user, 'Body metrics updated.');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => $request->validated('password'),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated.',
        ]);
    }

    /**
     * @return array{user: array<string, mixed>, metrics: array<string, mixed>}
     */
    private function profilePayload($user): array
    {
        $height = $this->numericValue($user->height);
        $weight = $this->numericValue($user->weight);
        $birthdate = $user->birthdate;
        $age = $birthdate ? $birthdate->age : null;
        $sex = $user->sex ?: null;
        $activity = $user->activity;
        $activityFactor = self::ACTIVITY_FACTORS[$activity] ?? self::ACTIVITY_FACTORS['sedentary'];
        $heightDisplayValue = $this->displayMetricValue($user->height_value, $height, 2);
        $heightDisplayUnit = $user->height_unit ?: 'm';
        $weightDisplayValue = $this->displayMetricValue($user->weight_value, $weight, 2);
        $weightDisplayUnit = $user->weight_unit ?: 'kg';

        $bmi = ($height !== null && $height > 0 && $weight !== null && $weight > 0)
            ? $weight / ($height * $height)
            : null;

        $canEstimateCalories = in_array($sex, ['male', 'female'], true)
            && $age !== null
            && $height !== null
            && $height > 0
            && $weight !== null
            && $weight > 0;

        $bmr = null;
        $tdee = null;

        if ($canEstimateCalories) {
            $heightInCentimeters = $height * 100;
            $sexAdjustment = $sex === 'male' ? 5 : -161;
            $bmr = (10 * $weight) + (6.25 * $heightInCentimeters) - (5 * $age) + $sexAdjustment;
            $tdee = $bmr * $activityFactor;
        }

        $fullName = trim($user->first_name.' '.$user->last_name);

        return [
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'fullName' => $fullName,
                'birthdate' => $birthdate?->toDateString(),
                'age' => $age,
                'height' => $height,
                'heightValue' => $heightDisplayValue,
                'heightUnit' => $heightDisplayUnit,
                'heightDisplay' => $this->metricDisplay($heightDisplayValue, $heightDisplayUnit),
                'weight' => $weight,
                'weightValue' => $weightDisplayValue,
                'weightUnit' => $weightDisplayUnit,
                'weightDisplay' => $this->metricDisplay($weightDisplayValue, $weightDisplayUnit),
                'activity' => $activity,
                'activityLabel' => self::ACTIVITY_LABELS[$activity] ?? 'Not set',
                'sex' => $sex,
                'sexLabel' => self::SEX_LABELS[$sex] ?? 'Not set',
            ],
            'metrics' => [
                'bmi' => $this->rounded($bmi, 2),
                'bmiCategory' => $this->bmiCategory($bmi),
                'bmiPercent' => $this->bmiPercent($bmi),
                'bmiStatus' => $this->bmiStatus($bmi),
                'bmr' => $this->rounded($bmr),
                'tdee' => $this->rounded($tdee),
                'maintenanceCalories' => $this->rounded($tdee),
                'mildDeficitCalories' => $this->rounded($tdee === null ? null : max($tdee - 250, 0)),
                'weightLossCalories' => $this->rounded($tdee === null ? null : max($tdee - 500, 0)),
                'highDeficitCalories' => $this->rounded($tdee === null ? null : max($tdee - 750, 0)),
                'proteinGrams' => $this->rounded($weight === null ? null : $weight * 1.6),
                'canEstimateCalories' => $canEstimateCalories,
            ],
        ];
    }

    private function profileJson($user, string $message): JsonResponse
    {
        $profile = $this->profilePayload($user->fresh());

        return response()->json([
            'success' => true,
            'message' => $message,
            'user' => $profile['user'],
            'metrics' => $profile['metrics'],
        ]);
    }

    private function numericValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function displayMetricValue(mixed $displayValue, ?float $fallbackValue, int $fallbackPrecision): ?string
    {
        if ($displayValue !== null && $displayValue !== '') {
            return (string) $displayValue;
        }

        if ($fallbackValue === null) {
            return null;
        }

        return number_format($fallbackValue, $fallbackPrecision, '.', '');
    }

    private function metricDisplay(?string $value, ?string $unit): string
    {
        if ($value === null || $value === '' || $unit === null || $unit === '') {
            return '--';
        }

        return "{$value} {$unit}";
    }

    private function rounded(?float $value, int $precision = 0): ?float
    {
        return $value === null ? null : round($value, $precision);
    }

    private function bmiCategory(?float $bmi): string
    {
        if ($bmi === null) {
            return 'Not available';
        }

        if ($bmi < 18.5) {
            return 'Underweight';
        }

        if ($bmi < 25) {
            return 'Normal weight';
        }

        if ($bmi < 30) {
            return 'Overweight';
        }

        return 'Obese';
    }

    private function bmiStatus(?float $bmi): string
    {
        return match ($this->bmiCategory($bmi)) {
            'Underweight' => 'Below the standard BMI range. Build toward steady nutrition, training, and recovery.',
            'Normal weight' => 'Within the standard BMI range. Keep training, nutrition, and recovery consistent.',
            'Overweight' => 'Above the standard BMI range. Use it as one signal while tracking strength, habits, and recovery.',
            'Obese' => 'High BMI range. Treat it as a screening signal and pair it with sustainable health markers.',
            default => 'Add height and weight to calculate BMI.',
        };
    }

    private function bmiPercent(?float $bmi): float
    {
        if ($bmi === null) {
            return 0;
        }

        $minimum = 14;
        $maximum = 40;

        return round(max(0, min(100, (($bmi - $minimum) / ($maximum - $minimum)) * 100)), 1);
    }
}
