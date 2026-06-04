@extends('layouts.app', ['title' => 'GreekGods | Profile'])

@push('styles')
    <link rel="stylesheet" href="/files/profile.css">
@endpush

@php
    $profileUser = $profile['user'];
    $metrics = $profile['metrics'];
    $formatNumber = fn ($value, int $precision = 0) => $value === null ? '--' : number_format((float) $value, $precision);
    $formatCalories = fn ($value) => $value === null ? '--' : number_format((float) $value).' kcal/day';
    $formatProtein = fn ($value) => $value === null ? '--' : number_format((float) $value).' g/day';
@endphp

@section('content')
    <script>
        const userId = @json($profileUser['id']);
        const workouts = @json($workouts);
        const profileState = @json($profile);
        const userData = profileState.user;
    </script>

    <nav>
        <button class="nav-menu-button" id="nav-menu-button" type="button">
            <img src="/graphics/svg/menu-black.svg" alt="Menu" title="Menu">
        </button>
        <div class="nav-logo">
            <img src="/graphics/logo/greekgodslogo.png" alt="GreekGods" title="GreekGods" onclick="window.location.href='/'">
        </div>
        <button class="nav-menu-profile" id="nav-menu-profile" type="button" onclick="window.location.href='/profile'">
            <img src="/graphics/svg/profile.svg" alt="Profile" title="Profile">
        </button>
        <ul class="nav-links" id="nav-links">
            <li><a href="/">HOME</a></li>
            <li><a href="/program">PROGRAM</a></li>
            <li><a href="/blog">BLOG</a></li>
            <li><a href="/calculator">CALCULATOR</a></li>
            <li><a href="/about">ABOUT</a></li>
        </ul>
        <div class="nav-button">
            <button id="logout" type="button">LOGOUT</button>
        </div>
    </nav>

    <main class="profile-shell">
        <section class="profile-hero" aria-labelledby="profile-title">
            <div class="hero-copy">
                <p class="eyebrow">Profile dashboard</p>
                <h1 id="profile-title">Welcome, <span id="hero-first-name">{{ $profileUser['firstName'] }}</span></h1>
                <p id="hero-summary">{{ $profileUser['fullName'] }} &middot; {{ $profileUser['email'] }}</p>
            </div>
            <dl class="quick-stats" aria-label="Body summary">
                <div>
                    <dt>Height</dt>
                    <dd id="height-summary">{{ $profileUser['heightDisplay'] }}</dd>
                </div>
                <div>
                    <dt>Weight</dt>
                    <dd id="weight-summary">{{ $profileUser['weightDisplay'] }}</dd>
                </div>
                <div>
                    <dt>Age</dt>
                    <dd>
                        <span id="age-summary">{{ $profileUser['age'] ?? '--' }}</span>
                        <small id="birthdate-summary">{{ $profileUser['birthdate'] ?? '--' }}</small>
                    </dd>
                </div>
                <div>
                    <dt>Activity</dt>
                    <dd id="activity-summary">{{ $profileUser['activityLabel'] }}</dd>
                </div>
                <div>
                    <dt>Sex</dt>
                    <dd id="sex-summary-stat">{{ $profileUser['sexLabel'] }}</dd>
                </div>
            </dl>
        </section>

        <section class="dashboard-grid" aria-label="Fitness dashboard">
            <div class="dashboard-column dashboard-column-main">
                <article class="profile-card bmi-card" aria-labelledby="bmi-title">
                    <div class="card-heading">
                        <div>
                            <p class="eyebrow">Body composition</p>
                            <h2 id="bmi-title">BMI</h2>
                        </div>
                        <span class="metric-chip" id="bmi-category">{{ $metrics['bmiCategory'] }}</span>
                    </div>
                    <div class="bmi-score">
                        <span id="bmi-value">{{ $formatNumber($metrics['bmi'], 2) }}</span>
                        <small>kg/m2</small>
                    </div>
                    <div class="bmi-gauge" style="--bmi-position: {{ $metrics['bmiPercent'] }}%;">
                        <div class="bmi-track" aria-hidden="true">
                            <span class="bmi-band bmi-underweight"></span>
                            <span class="bmi-band bmi-normal"></span>
                            <span class="bmi-band bmi-overweight"></span>
                            <span class="bmi-band bmi-obese"></span>
                            <span class="bmi-marker" id="bmi-marker"></span>
                        </div>
                        <div class="bmi-scale" aria-hidden="true">
                            <span>18.5</span>
                            <span>25</span>
                            <span>30</span>
                        </div>
                    </div>
                    <p class="metric-note" id="bmi-status">{{ $metrics['bmiStatus'] }}</p>
                </article>

                <article class="profile-card metrics-card" aria-labelledby="fitness-metrics-title">
                    <div class="card-heading">
                        <div>
                            <p class="eyebrow">Energy targets</p>
                            <h2 id="fitness-metrics-title">Fitness metrics</h2>
                        </div>
                        <span class="metric-chip" id="sex-summary">{{ $profileUser['sexLabel'] }}</span>
                    </div>
                    <dl class="metric-list">
                        <div>
                            <dt>BMR</dt>
                            <dd id="bmr-value">{{ $formatCalories($metrics['bmr']) }}</dd>
                        </div>
                        <div>
                            <dt>TDEE</dt>
                            <dd id="tdee-value">{{ $formatCalories($metrics['tdee']) }}</dd>
                        </div>
                        <div>
                            <dt>Maintain</dt>
                            <dd id="maintain-value">{{ $formatCalories($metrics['maintenanceCalories']) }}</dd>
                        </div>
                        <div>
                            <dt>Mild deficit</dt>
                            <dd id="mild-deficit-value">{{ $formatCalories($metrics['mildDeficitCalories']) }}</dd>
                        </div>
                        <div>
                            <dt>Weight loss</dt>
                            <dd id="weight-loss-value">{{ $formatCalories($metrics['weightLossCalories']) }}</dd>
                        </div>
                        <div>
                            <dt>Protein</dt>
                            <dd id="protein-value">{{ $formatProtein($metrics['proteinGrams']) }}</dd>
                        </div>
                    </dl>
                    <p class="metric-note {{ $metrics['canEstimateCalories'] ? 'is-hidden' : '' }}" id="calorie-estimate-note">
                        Complete sex in Body Metrics to estimate BMR and TDEE.
                    </p>
                </article>
            </div>

            <div class="dashboard-column dashboard-column-side">
                <article class="profile-card workouts-card" aria-labelledby="today-workout-title">
                    <div class="card-heading">
                        <div>
                            <p class="eyebrow" id="date-today">{{ now()->format('M d, Y') }}</p>
                            <h2 id="today-workout-title">Today's workout</h2>
                        </div>
                        <span class="metric-chip" id="weekday-today">{{ now()->format('l') }}</span>
                    </div>
                    <div class="workout-table-wrap">
                        <table class="workouts">
                            <thead>
                                <tr>
                                    <th>Workout</th>
                                    <th>Focus</th>
                                    <th>Sets / Reps</th>
                                </tr>
                            </thead>
                            <tbody id="workout-table-body">
                                @forelse ($workouts as $workout)
                                    <tr>
                                        <td>{{ $workout->workoutName }}</td>
                                        <td>{{ $workout->workoutFocus ?? '--' }}</td>
                                        <td>
                                            @if ($workout->workoutSets && $workout->workoutReps)
                                                {{ $workout->workoutSets }} x {{ $workout->workoutReps }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-workout>
                                        <td colspan="3">No workouts scheduled for today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a class="primary-link workout-action" href="/program">Open program</a>
                </article>
            </div>
        </section>

        <section class="settings-section" aria-labelledby="profile-settings-title">
            <div class="section-heading">
                <p class="eyebrow">Settings</p>
                <h2 id="profile-settings-title">Edit information</h2>
            </div>

            <div class="settings-grid">
                <article class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3>Account</h3>
                            <p id="account-settings-summary">{{ $profileUser['fullName'] }} &middot; {{ $profileUser['email'] }}</p>
                        </div>
                        <button class="ghost-button" type="button" data-settings-toggle="account-settings-form" aria-controls="account-settings-form" aria-expanded="false">Edit</button>
                    </div>
                    <form id="account-settings-form" class="settings-form" action="/profile/account" method="POST" hidden novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="form-grid">
                            <div class="field">
                                <label for="account-first-name">First name</label>
                                <input id="account-first-name" name="first_name" type="text" value="{{ $profileUser['firstName'] }}" autocomplete="given-name">
                                <p class="field-error" data-field-error="first_name"></p>
                            </div>
                            <div class="field">
                                <label for="account-last-name">Last name</label>
                                <input id="account-last-name" name="last_name" type="text" value="{{ $profileUser['lastName'] }}" autocomplete="family-name">
                                <p class="field-error" data-field-error="last_name"></p>
                            </div>
                            <div class="field field-wide">
                                <label for="account-email">Email</label>
                                <input id="account-email" name="email" type="email" value="{{ $profileUser['email'] }}" autocomplete="email">
                                <p class="field-error" data-field-error="email"></p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="secondary-button" type="button" data-settings-cancel="account-settings-form">Cancel</button>
                            <button class="primary-button" type="submit">Save account</button>
                        </div>
                        <p class="form-status" data-form-status role="status"></p>
                    </form>
                </article>

                <article class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3>Body Metrics</h3>
                            <p id="body-settings-summary">{{ $profileUser['heightDisplay'] }} &middot; {{ $profileUser['weightDisplay'] }} &middot; {{ $profileUser['activityLabel'] }}</p>
                        </div>
                        <button class="ghost-button" type="button" data-settings-toggle="body-settings-form" aria-controls="body-settings-form" aria-expanded="false">Edit</button>
                    </div>
                    <form id="body-settings-form" class="settings-form" action="/profile/body-metrics" method="POST" hidden novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="form-grid">
                            <div class="field">
                                <label for="body-birthdate">Birthdate</label>
                                <input id="body-birthdate" name="birthdate" type="date" value="{{ $profileUser['birthdate'] }}" min="1923-01-01" max="2011-01-01">
                                <p class="field-error" data-field-error="birthdate"></p>
                            </div>
                            <div class="field">
                                <label for="body-sex">Sex</label>
                                <select id="body-sex" name="sex">
                                    <option value="" @selected($profileUser['sex'] === null)>Add later</option>
                                    <option value="male" @selected($profileUser['sex'] === 'male')>Male</option>
                                    <option value="female" @selected($profileUser['sex'] === 'female')>Female</option>
                                    <option value="prefer_not_to_say" @selected($profileUser['sex'] === 'prefer_not_to_say')>Prefer not to say</option>
                                </select>
                                <p class="field-error" data-field-error="sex"></p>
                            </div>
                            <div class="field">
                                <label for="body-height">Height</label>
                                <input id="body-height" name="height_value" type="number" value="{{ $profileUser['heightValue'] }}" min="0" step="0.01" inputmode="decimal">
                                <p class="field-error" data-field-error="height_value"></p>
                            </div>
                            <div class="field">
                                <label for="body-height-unit">Height unit</label>
                                <select id="body-height-unit" name="height_unit">
                                    <option value="cm" @selected($profileUser['heightUnit'] === 'cm')>cm</option>
                                    <option value="m" @selected($profileUser['heightUnit'] === 'm')>m</option>
                                    <option value="in" @selected($profileUser['heightUnit'] === 'in')>in</option>
                                    <option value="ft" @selected($profileUser['heightUnit'] === 'ft')>ft</option>
                                </select>
                                <p class="field-error" data-field-error="height_unit"></p>
                            </div>
                            <div class="field">
                                <label for="body-weight">Weight</label>
                                <input id="body-weight" name="weight_value" type="number" value="{{ $profileUser['weightValue'] }}" min="0" step="0.1" inputmode="decimal">
                                <p class="field-error" data-field-error="weight_value"></p>
                            </div>
                            <div class="field">
                                <label for="body-weight-unit">Weight unit</label>
                                <select id="body-weight-unit" name="weight_unit">
                                    <option value="kg" @selected($profileUser['weightUnit'] === 'kg')>kg</option>
                                    <option value="lb" @selected($profileUser['weightUnit'] === 'lb')>lb</option>
                                </select>
                                <p class="field-error" data-field-error="weight_unit"></p>
                            </div>
                            <div class="field field-wide">
                                <label for="body-activity">Activity</label>
                                <select id="body-activity" name="activity">
                                    <option value="sedentary" @selected($profileUser['activity'] === 'sedentary')>Sedentary: little or no exercise</option>
                                    <option value="light" @selected($profileUser['activity'] === 'light')>Light: exercise 1-3 times/week</option>
                                    <option value="moderate" @selected($profileUser['activity'] === 'moderate')>Moderate: exercise 3-5 times/week</option>
                                    <option value="active" @selected($profileUser['activity'] === 'active')>Active: intense exercise 6-7 times/week</option>
                                    <option value="very_active" @selected($profileUser['activity'] === 'very_active')>Very active: intense daily exercise or physical work</option>
                                </select>
                                <p class="field-error" data-field-error="activity"></p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="secondary-button" type="button" data-settings-cancel="body-settings-form">Cancel</button>
                            <button class="primary-button" type="submit">Save metrics</button>
                        </div>
                        <p class="form-status" data-form-status role="status"></p>
                    </form>
                </article>

                <article class="settings-card">
                    <div class="settings-card-header">
                        <div>
                            <h3>Security</h3>
                            <p>Password changes require your current password.</p>
                        </div>
                        <button class="ghost-button" type="button" data-settings-toggle="security-settings-form" aria-controls="security-settings-form" aria-expanded="false">Edit</button>
                    </div>
                    <form id="security-settings-form" class="settings-form" action="/profile/password" method="POST" hidden novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="form-grid">
                            <div class="field field-wide">
                                <label for="security-current-password">Current password</label>
                                <input id="security-current-password" name="current_password" type="password" autocomplete="current-password">
                                <p class="field-error" data-field-error="current_password"></p>
                            </div>
                            <div class="field">
                                <label for="security-password">New password</label>
                                <input id="security-password" name="password" type="password" autocomplete="new-password">
                                <p class="field-error" data-field-error="password"></p>
                            </div>
                            <div class="field">
                                <label for="security-password-confirmation">Confirm new password</label>
                                <input id="security-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                                <p class="field-error" data-field-error="password_confirmation"></p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="secondary-button" type="button" data-settings-cancel="security-settings-form">Cancel</button>
                            <button class="primary-button" type="submit">Update password</button>
                        </div>
                        <p class="form-status" data-form-status role="status"></p>
                    </form>
                </article>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="/index.js"></script>
    <script src="/files/profile.js"></script>
@endpush
