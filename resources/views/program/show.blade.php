@extends('layouts.app', ['title' => 'GreekGods | Program'])

@push('styles')
    @vite(['resources/css/site.css', 'resources/css/pages/program.css'])
@endpush

@section('content')
    <script>
        const userId = @json($user->id);
        const userFullName = @json(trim($user->first_name.' '.$user->last_name));
        const splitCatalog = @json($splitCatalog);
        const workoutTemplates = @json($workoutTemplates);
        const programState = @json(['program' => $programState, 'workouts' => $workouts]);
    </script>

    <x-site-nav />

    <header class="program-hero" data-builder-chrome>
        <div class="program-shell">
            <p class="eyebrow">Program builder</p>
            <h1>Build your training week.</h1>
            <p>Choose a structure, tailor every session, and keep your week easy to scan.</p>
        </div>
    </header>

    <main class="program-shell program-main">
        <ol class="stepper" aria-label="Program setup progress" data-builder-chrome>
            <li data-step-indicator="selection"><span>1</span><strong>Split & schedule</strong></li>
            <li data-step-indicator="draft"><span>2</span><strong>Edit workouts</strong></li>
            <li data-step-indicator="summary"><span>3</span><strong>Your program</strong></li>
        </ol>

        <aside class="workflow-guide" id="workflow-guide" aria-labelledby="workflow-guide-title">
            <span aria-hidden="true">→</span>
            <div>
                <strong id="workflow-guide-title">What to do next</strong>
                <p id="workflow-guide-message" aria-live="polite">Choose a workout split to begin.</p>
            </div>
        </aside>

        <p id="program-status" class="sr-status" role="status" aria-live="polite"></p>

        <section class="program-page" data-program-page="selection" aria-labelledby="selection-title">
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Step 1</p>
                    <h2 id="selection-title">Choose your split</h2>
                    <p>Pick the structure that fits your week, then choose its schedule.</p>
                </div>
            </div>

            <div class="split-grid" id="split-options" aria-label="Workout splits"></div>

            <div class="schedule-panel">
                <div class="schedule-controls">
                    <label for="schedule-options">Weekly schedule</label>
                    <select id="schedule-options" disabled>
                        <option value="">Select a split first</option>
                    </select>
                    <p id="schedule-summary">Choose a split to see available schedules.</p>
                </div>
                <div class="schedule-preview" id="schedule-preview" aria-label="Weekly schedule preview"></div>
            </div>

            <div class="page-actions page-actions--end">
                <button class="button button--primary" id="selection-next" type="button" disabled>NEXT: EDIT WORKOUTS</button>
            </div>
        </section>

        <section class="program-page" data-program-page="draft" aria-labelledby="draft-title" hidden>
            <div class="page-heading">
                <div>
                    <p class="eyebrow">Step 2</p>
                    <h2 id="draft-title">Make it yours</h2>
                    <p>Edit the generated exercises, sets, and rep ranges before saving.</p>
                </div>
                <div class="selection-chip" id="draft-selection-label"></div>
            </div>

            <div class="week-carousel" data-week-carousel>
                <div class="carousel-toolbar">
                    <p>Weekly schedule <span id="draft-carousel-progress" aria-live="polite">Day 1 of 7</span></p>
                    <div class="carousel-actions">
                        <button type="button" data-carousel-previous aria-label="Show previous day" aria-controls="draft-board">←</button>
                        <button type="button" data-carousel-next aria-label="Show next day" aria-controls="draft-board">→</button>
                    </div>
                </div>
                <div class="carousel-frame">
                    <div class="weekly-board weekly-board--draft" id="draft-board" tabindex="0" aria-label="Editable weekly workout schedule"></div>
                </div>
            </div>

            <div class="page-actions">
                <button class="button button--secondary" id="draft-back" type="button">BACK</button>
                <button class="button button--primary" id="save-program" type="button">SAVE PROGRAM</button>
            </div>
        </section>

        <section class="program-page" data-program-page="summary" aria-labelledby="summary-title" hidden>
            <div class="summary-hero">
                <div>
                    <p class="eyebrow">Your current program</p>
                    <h2 id="summary-title"></h2>
                    <p id="summary-schedule"></p>
                </div>
                <div class="summary-actions">
                    <button class="button button--secondary" id="change-program" type="button">CHANGE PROGRAM</button>
                </div>
            </div>

            <div class="week-carousel" data-week-carousel>
                <div class="carousel-toolbar">
                    <p>Your week <span id="summary-carousel-progress" aria-live="polite">Day 1 of 7</span></p>
                    <div class="carousel-actions">
                        <button type="button" data-carousel-previous aria-label="Show previous day" aria-controls="summary-board">←</button>
                        <button type="button" data-carousel-next aria-label="Show next day" aria-controls="summary-board">→</button>
                    </div>
                </div>
                <div class="carousel-frame">
                    <div class="weekly-board weekly-board--summary" id="summary-board" tabindex="0" aria-label="Saved weekly workout schedule"></div>
                </div>
            </div>
        </section>
    </main>

    <div class="workout-modal" id="workout-modal" hidden>
        <form class="workout-form" id="workout-form" aria-labelledby="workout-form-title">
            <div class="modal-heading">
                <div>
                    <p class="eyebrow" id="workout-form-day">Training day</p>
                    <h2 id="workout-form-title">Add workout</h2>
                </div>
                <button id="workout-form-cancel" class="modal-close" type="button" aria-label="Close workout form">&times;</button>
            </div>
            <input type="hidden" id="workout-day">
            <label for="workout-name">Exercise</label>
            <input type="text" id="workout-name" maxlength="120" placeholder="Bench Press" autocomplete="off" required>
            <div class="autocomplete-suggestions"></div>
            <label for="workout-focus">Muscle focus <span>Optional</span></label>
            <input type="text" id="workout-focus" maxlength="120" placeholder="Chest">
            <div class="form-metrics">
                <div>
                    <label for="workout-sets">Sets</label>
                    <input type="number" min="1" max="50" id="workout-sets" required>
                </div>
                <div>
                    <label for="workout-reps-min">Min reps</label>
                    <input type="number" min="1" max="100" id="workout-reps-min" required>
                </div>
                <div>
                    <label for="workout-reps-max">Max reps</label>
                    <input type="number" min="1" max="100" id="workout-reps-max" required>
                </div>
            </div>
            <p class="field-error" id="workout-form-error" role="alert"></p>
            <button class="button button--primary button--full" id="form-add" type="submit">SAVE WORKOUT</button>
        </form>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/site.js', 'resources/js/pages/program.js'])
@endpush
