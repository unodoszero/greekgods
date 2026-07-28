<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Workout;
use App\Support\AuthSessionIdentity;
use App\Support\WorkoutSplitCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function show(Request $request): View
    {
        $user = Auth::user();
        AuthSessionIdentity::store($request, $user);
        $program = $user->programs()->with('workouts')->first();
        $programState = null;
        $workouts = [];

        if ($program) {
            $split = WorkoutSplitCatalog::normalizeSplit($program->program);
            $schedule = $split ? WorkoutSplitCatalog::normalizeSchedule($split, $program->schedule) : null;

            if ($split && $schedule) {
                $programState = [
                    'id' => $program->id,
                    'split' => $split,
                    'schedule' => $schedule,
                ];
                $workouts = $program->workouts->map(fn (Workout $workout): array => $this->workoutPayload($workout))->values();
            }
        }

        return view('program.show', [
            'user' => $user,
            'programState' => $programState,
            'workouts' => $workouts,
            'splitCatalog' => WorkoutSplitCatalog::all(),
            'workoutTemplates' => config('preconfigured_workouts'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'split' => ['required', 'string'],
            'schedule' => ['required', 'string'],
            'workouts' => ['required', 'array', 'min:1'],
            'workouts.*.day' => ['required', 'string'],
            'workouts.*.name' => ['required', 'string', 'max:120'],
            'workouts.*.focus' => ['nullable', 'string', 'max:120'],
            'workouts.*.sets' => ['required', 'integer', 'min:1', 'max:50'],
            'workouts.*.reps_min' => ['required', 'integer', 'min:1', 'max:100'],
            'workouts.*.reps_max' => ['required', 'integer', 'min:1', 'max:100'],
            'workouts.*.position' => ['required', 'integer', 'min:0', 'distinct'],
        ]);

        $split = WorkoutSplitCatalog::normalizeSplit($validated['split']);
        $schedule = $split ? WorkoutSplitCatalog::normalizeSchedule($split, $validated['schedule']) : null;

        if ($split === null || $schedule === null) {
            throw ValidationException::withMessages(['schedule' => 'Select a valid split and schedule.']);
        }

        foreach ($validated['workouts'] as $index => $workout) {
            if (! WorkoutSplitCatalog::isTrainingDay($split, $schedule, $workout['day'])) {
                throw ValidationException::withMessages([
                    "workouts.{$index}.day" => 'Workouts can only be added to configured training days.',
                ]);
            }

            if ((int) $workout['reps_min'] > (int) $workout['reps_max']) {
                throw ValidationException::withMessages([
                    "workouts.{$index}.reps_min" => 'Minimum reps cannot be greater than maximum reps.',
                ]);
            }
        }

        $program = DB::transaction(function () use ($split, $schedule, $validated): Program {
            Auth::user()->programs()->delete();

            $program = Auth::user()->programs()->create([
                'program' => $split,
                'schedule' => $schedule,
            ]);

            foreach ($validated['workouts'] as $workout) {
                $program->workouts()->create($this->workoutAttributes($workout));
            }

            return $program->load('workouts');
        });

        return response()->json([
            'success' => true,
            'message' => 'Program saved.',
            'program' => ['id' => $program->id, 'split' => $split, 'schedule' => $schedule],
            'workouts' => $program->workouts->map(fn (Workout $workout): array => $this->workoutPayload($workout))->values(),
        ]);
    }

    public function storeWorkout(Request $request): JsonResponse
    {
        $program = $this->currentProgram();
        $data = $this->validatedWorkout($request);
        $this->ensureTrainingDay($program, $data['day']);

        $position = (int) $program->workouts()->max('position') + 1;
        $workout = $program->workouts()->create($this->workoutAttributes([...$data, 'position' => $position]));

        return response()->json([
            'success' => true,
            'message' => 'Workout added.',
            'workout' => $this->workoutPayload($workout),
        ]);
    }

    public function updateWorkout(Request $request, int $workoutId): JsonResponse
    {
        $program = $this->currentProgram();
        $workout = $program->workouts()->findOrFail($workoutId);
        $data = $this->validatedWorkout($request);
        $this->ensureTrainingDay($program, $data['day']);

        $workout->fill($this->workoutAttributes([...$data, 'position' => $workout->position]))->save();

        return response()->json([
            'success' => true,
            'message' => 'Workout updated.',
            'workout' => $this->workoutPayload($workout),
        ]);
    }

    public function destroyWorkout(int $workoutId): JsonResponse
    {
        $program = $this->currentProgram();
        $workout = $program->workouts()->findOrFail($workoutId);
        $position = $workout->position;

        DB::transaction(function () use ($program, $workout, $position): void {
            $workout->delete();
            $program->workouts()
                ->where('position', '>', $position)
                ->orderBy('position')
                ->get()
                ->each(function (Workout $remaining): void {
                    $remaining->decrement('position');
                });
        });

        return response()->json(['success' => true, 'message' => 'Workout removed.']);
    }

    public function destroy(): JsonResponse
    {
        Auth::user()->programs()->delete();

        return response()->json(['success' => true, 'message' => 'Program removed.']);
    }

    private function currentProgram(): Program
    {
        $program = Auth::user()->programs()->first();

        if (! $program) {
            throw ValidationException::withMessages([
                'program' => 'Save a workout split before managing workouts.',
            ]);
        }

        return $program;
    }

    private function ensureTrainingDay(Program $program, string $day): void
    {
        $split = WorkoutSplitCatalog::normalizeSplit($program->program);
        $schedule = $split ? WorkoutSplitCatalog::normalizeSchedule($split, $program->schedule) : null;

        if ($split === null || $schedule === null || ! WorkoutSplitCatalog::isTrainingDay($split, $schedule, $day)) {
            throw ValidationException::withMessages([
                'day' => 'Workouts can only be added to configured training days.',
            ]);
        }
    }

    /**
     * @return array{day: string, name: string, focus: string|null, sets: int, reps_min: int, reps_max: int}
     */
    private function validatedWorkout(Request $request): array
    {
        $data = $request->validate([
            'day' => ['required', 'string'],
            'name' => ['required', 'string', 'max:120'],
            'focus' => ['nullable', 'string', 'max:120'],
            'sets' => ['required', 'integer', 'min:1', 'max:50'],
            'reps_min' => ['required', 'integer', 'min:1', 'max:100', 'lte:reps_max'],
            'reps_max' => ['required', 'integer', 'min:1', 'max:100', 'gte:reps_min'],
        ]);

        return [
            'day' => $data['day'],
            'name' => trim($data['name']),
            'focus' => $this->nullableString($data['focus'] ?? null),
            'sets' => (int) $data['sets'],
            'reps_min' => (int) $data['reps_min'],
            'reps_max' => (int) $data['reps_max'],
        ];
    }

    /**
     * @param  array<string, mixed>  $workout
     * @return array<string, mixed>
     */
    private function workoutAttributes(array $workout): array
    {
        return [
            'workout_day' => $workout['day'],
            'workout_name' => trim($workout['name']),
            'workout_focus' => $this->nullableString($workout['focus'] ?? null),
            'workout_sets' => (int) $workout['sets'],
            'reps_min' => (int) $workout['reps_min'],
            'reps_max' => (int) $workout['reps_max'],
            'position' => (int) $workout['position'],
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    /**
     * @return array<string, mixed>
     */
    private function workoutPayload(Workout $workout): array
    {
        return [
            'id' => $workout->id,
            'workoutDay' => $workout->workout_day,
            'workoutName' => $workout->workout_name,
            'workoutFocus' => $workout->workout_focus,
            'workoutSets' => $workout->workout_sets,
            'repsMin' => $workout->reps_min,
            'repsMax' => $workout->reps_max,
            'position' => $workout->position,
        ];
    }
}
