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
        $program = $user->programs()->latest('id')->first();
        $workouts = $user->workouts()
            ->orderBy('id')
            ->get(['id', 'workout_name as workoutName', 'workout_focus as workoutFocus', 'workout_reps as workoutReps', 'workout_sets as workoutSets', 'workout_day as workoutDay']);
        $programState = null;

        if ($program) {
            $split = WorkoutSplitCatalog::normalizeSplit($program->program);
            $schedule = $split ? WorkoutSplitCatalog::normalizeSchedule($split, $program->schedule) : null;

            if ($split && $schedule) {
                $programState = [
                    'id' => $program->id,
                    'split' => $split,
                    'schedule' => $schedule,
                ];
            }
        }

        return view('program.show', [
            'user' => $user,
            'program' => $program,
            'programState' => $programState,
            'workouts' => $workouts,
            'splitCatalog' => WorkoutSplitCatalog::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'split' => ['required', 'string'],
            'schedule' => ['nullable', 'string'],
            'days' => ['nullable', 'string'],
            'workouts' => ['nullable'],
        ]);

        $split = WorkoutSplitCatalog::normalizeSplit($request->string('split')->toString());
        $schedule = $split ? WorkoutSplitCatalog::normalizeSchedule($split, $request->input('schedule', $request->input('days'))) : null;

        if ($split === null || $schedule === null) {
            throw ValidationException::withMessages([
                'schedule' => 'Select a valid split and schedule.',
            ]);
        }

        $hasWorkoutPayload = $request->has('workouts')
            && $request->input('workouts') !== null
            && $request->input('workouts') !== '';
        $workouts = $hasWorkoutPayload
            ? $this->optionalWorkoutPayload($request->input('workouts'))
            : $this->preconfiguredWorkouts($split, $schedule);

        $user = Auth::user();

        $createdWorkouts = DB::transaction(function () use ($user, $split, $schedule, $workouts, $hasWorkoutPayload): array {
            $user->programs()->delete();
            $user->workouts()->delete();

            Program::create([
                'user_id' => $user->id,
                'program' => $split,
                'schedule' => $schedule,
            ]);

            $createdWorkouts = [];

            foreach ($workouts as $workout) {
                $day = (string) ($workout['workoutDay'] ?? $workout['day'] ?? '');

                if (! WorkoutSplitCatalog::isTrainingDay($split, $schedule, $day)) {
                    throw ValidationException::withMessages([
                        'day' => 'Workouts can only be added to configured training days.',
                    ]);
                }

                $created = $user->workouts()->create([
                    'workout_day' => $day,
                    'workout_name' => (string) ($workout['workoutName'] ?? $workout['workout_name'] ?? ''),
                    'workout_focus' => $hasWorkoutPayload ? null : $this->nullableString($workout['workoutFocus'] ?? $workout['workout_focus'] ?? null),
                    'workout_sets' => $hasWorkoutPayload ? $this->positiveInt($workout['workoutSets'] ?? $workout['workout_sets'] ?? null) : null,
                    'workout_reps' => $hasWorkoutPayload ? $this->positiveInt($workout['workoutReps'] ?? $workout['workout_reps'] ?? null) : null,
                ]);

                $createdWorkouts[] = $this->workoutPayload($created);
            }

            return $createdWorkouts;
        });

        return response()->json([
            'success' => true,
            'message' => $hasWorkoutPayload ? 'Program saved.' : 'Program saved. Preconfigured workouts added.',
            'program' => [
                'split' => $split,
                'schedule' => $schedule,
            ],
            'workouts' => $createdWorkouts,
        ]);
    }

    public function storeWorkout(Request $request): JsonResponse
    {
        $program = $this->currentProgram();
        $data = $this->validatedWorkout($request);

        $this->ensureTrainingDay($program, $data['day']);

        $workout = Auth::user()->workouts()->create([
            'workout_day' => $data['day'],
            'workout_name' => $data['workout_name'],
            'workout_focus' => null,
            'workout_sets' => $data['workout_sets'],
            'workout_reps' => $data['workout_reps'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workout added.',
            'workout' => $this->workoutPayload($workout),
        ]);
    }

    public function updateWorkout(Request $request, Workout $workout): JsonResponse
    {
        $this->authorizeWorkout($workout);
        $program = $this->currentProgram();
        $data = $this->validatedWorkout($request);

        $this->ensureTrainingDay($program, $data['day']);

        $workout->fill([
            'workout_day' => $data['day'],
            'workout_name' => $data['workout_name'],
            'workout_focus' => null,
            'workout_sets' => $data['workout_sets'],
            'workout_reps' => $data['workout_reps'],
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Workout updated.',
            'workout' => $this->workoutPayload($workout),
        ]);
    }

    public function destroyWorkout(int $workoutId): JsonResponse
    {
        $deleted = Workout::query()
            ->whereKey($workoutId)
            ->where('user_id', Auth::id())
            ->delete();

        abort_if($deleted === 0, 404);

        return response()->json([
            'success' => true,
            'message' => 'Workout removed.',
        ]);
    }

    public function destroy(): JsonResponse
    {
        $userId = Auth::id();

        DB::transaction(function () use ($userId): void {
            Workout::query()->where('user_id', $userId)->delete();
            Program::query()->where('user_id', $userId)->delete();
        });

        return response()->json(['success' => true, 'message' => 'Program and workouts deleted successfully']);
    }

    private function currentProgram(): Program
    {
        $program = Auth::user()->programs()->latest('id')->first();

        if (! $program) {
            throw ValidationException::withMessages([
                'program' => 'Save a workout split before adding workouts.',
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
     * @return array{day: string, workout_name: string, workout_sets: int, workout_reps: int}
     */
    private function validatedWorkout(Request $request): array
    {
        $data = $request->validate([
            'day' => ['required', 'string'],
            'workout_name' => ['required', 'string', 'max:120'],
            'workout_sets' => ['required', 'integer', 'min:1', 'max:50'],
            'workout_reps' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        return [
            'day' => $data['day'],
            'workout_name' => $data['workout_name'],
            'workout_sets' => (int) $data['workout_sets'],
            'workout_reps' => (int) $data['workout_reps'],
        ];
    }

    private function authorizeWorkout(Workout $workout): void
    {
        abort_unless($workout->user_id === Auth::id(), 404);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function optionalWorkoutPayload(mixed $payload): array
    {
        if ($payload === null || $payload === '') {
            return [];
        }

        $workouts = is_string($payload) ? json_decode($payload, true) : $payload;

        if (! is_array($workouts)) {
            throw ValidationException::withMessages([
                'workouts' => 'Invalid workouts payload.',
            ]);
        }

        return $workouts;
    }

    /**
     * @return array<int, array{day: string, workout_name: string, workout_focus: string|null}>
     */
    private function preconfiguredWorkouts(string $split, string $schedule): array
    {
        $templates = config("preconfigured_workouts.{$split}", []);
        $days = WorkoutSplitCatalog::daysFor($split, $schedule) ?? [];
        $workouts = [];

        foreach ($days as $weekday => $dayLabel) {
            if ($dayLabel === 'Rest') {
                continue;
            }

            foreach (($templates[$dayLabel] ?? []) as $template) {
                $workouts[] = [
                    'day' => $weekday,
                    'workout_name' => (string) ($template['name'] ?? ''),
                    'workout_focus' => $this->nullableString($template['focus'] ?? null),
                ];
            }
        }

        return $workouts;
    }

    private function positiveInt(mixed $value): int
    {
        $int = (int) $value;

        if ($int < 1) {
            throw ValidationException::withMessages([
                'workouts' => 'Workout sets and reps must be positive numbers.',
            ]);
        }

        return $int;
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
            'workoutReps' => $workout->workout_reps,
        ];
    }
}
