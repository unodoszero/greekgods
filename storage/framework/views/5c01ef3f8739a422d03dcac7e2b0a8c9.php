<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/files/profile.css">
<?php $__env->stopPush(); ?>

<?php
    $profileUser = $profile['user'];
    $metrics = $profile['metrics'];
    $formatNumber = fn ($value, int $precision = 0) => $value === null ? '--' : number_format((float) $value, $precision);
    $formatCalories = fn ($value) => $value === null ? '--' : number_format((float) $value).' kcal/day';
    $formatProtein = fn ($value) => $value === null ? '--' : number_format((float) $value).' g/day';
?>

<?php $__env->startSection('content'); ?>
    <script>
        const userId = <?php echo json_encode($profileUser['id'], 15, 512) ?>;
        const workouts = <?php echo json_encode($workouts, 15, 512) ?>;
        const profileState = <?php echo json_encode($profile, 15, 512) ?>;
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
                <h1 id="profile-title">Welcome, <span id="hero-first-name"><?php echo e($profileUser['firstName']); ?></span></h1>
                <p id="hero-summary"><?php echo e($profileUser['fullName']); ?> &middot; <?php echo e($profileUser['email']); ?></p>
            </div>
            <dl class="quick-stats" aria-label="Body summary">
                <div>
                    <dt>Height</dt>
                    <dd id="height-summary"><?php echo e($profileUser['heightDisplay']); ?></dd>
                </div>
                <div>
                    <dt>Weight</dt>
                    <dd id="weight-summary"><?php echo e($profileUser['weightDisplay']); ?></dd>
                </div>
                <div>
                    <dt>Age</dt>
                    <dd>
                        <span id="age-summary"><?php echo e($profileUser['age'] ?? '--'); ?></span>
                        <small id="birthdate-summary"><?php echo e($profileUser['birthdate'] ?? '--'); ?></small>
                    </dd>
                </div>
                <div>
                    <dt>Activity</dt>
                    <dd id="activity-summary"><?php echo e($profileUser['activityLabel']); ?></dd>
                </div>
                <div>
                    <dt>Sex</dt>
                    <dd id="sex-summary-stat"><?php echo e($profileUser['sexLabel']); ?></dd>
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
                        <span class="metric-chip" id="bmi-category"><?php echo e($metrics['bmiCategory']); ?></span>
                    </div>
                    <div class="bmi-score">
                        <span id="bmi-value"><?php echo e($formatNumber($metrics['bmi'], 2)); ?></span>
                        <small>kg/m2</small>
                    </div>
                    <div class="bmi-gauge" style="--bmi-position: <?php echo e($metrics['bmiPercent']); ?>%;">
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
                    <p class="metric-note" id="bmi-status"><?php echo e($metrics['bmiStatus']); ?></p>
                </article>

                <article class="profile-card metrics-card" aria-labelledby="fitness-metrics-title">
                    <div class="card-heading">
                        <div>
                            <p class="eyebrow">Energy targets</p>
                            <h2 id="fitness-metrics-title">Fitness metrics</h2>
                        </div>
                        <span class="metric-chip" id="sex-summary"><?php echo e($profileUser['sexLabel']); ?></span>
                    </div>
                    <dl class="metric-list">
                        <div>
                            <dt>BMR</dt>
                            <dd id="bmr-value"><?php echo e($formatCalories($metrics['bmr'])); ?></dd>
                        </div>
                        <div>
                            <dt>TDEE</dt>
                            <dd id="tdee-value"><?php echo e($formatCalories($metrics['tdee'])); ?></dd>
                        </div>
                        <div>
                            <dt>Maintain</dt>
                            <dd id="maintain-value"><?php echo e($formatCalories($metrics['maintenanceCalories'])); ?></dd>
                        </div>
                        <div>
                            <dt>Mild deficit</dt>
                            <dd id="mild-deficit-value"><?php echo e($formatCalories($metrics['mildDeficitCalories'])); ?></dd>
                        </div>
                        <div>
                            <dt>Weight loss</dt>
                            <dd id="weight-loss-value"><?php echo e($formatCalories($metrics['weightLossCalories'])); ?></dd>
                        </div>
                        <div>
                            <dt>Protein</dt>
                            <dd id="protein-value"><?php echo e($formatProtein($metrics['proteinGrams'])); ?></dd>
                        </div>
                    </dl>
                    <p class="metric-note <?php echo e($metrics['canEstimateCalories'] ? 'is-hidden' : ''); ?>" id="calorie-estimate-note">
                        Complete sex in Body Metrics to estimate BMR and TDEE.
                    </p>
                </article>
            </div>

            <div class="dashboard-column dashboard-column-side">
                <article class="profile-card workouts-card" aria-labelledby="today-workout-title">
                    <div class="card-heading">
                        <div>
                            <p class="eyebrow" id="date-today"><?php echo e(now()->format('M d, Y')); ?></p>
                            <h2 id="today-workout-title">Today's workout</h2>
                        </div>
                        <span class="metric-chip" id="weekday-today"><?php echo e(now()->format('l')); ?></span>
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
                                <?php $__empty_1 = true; $__currentLoopData = $workouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($workout->workoutName); ?></td>
                                        <td><?php echo e($workout->workoutFocus ?? '--'); ?></td>
                                        <td>
                                            <?php if($workout->workoutSets && $workout->workoutReps): ?>
                                                <?php echo e($workout->workoutSets); ?> x <?php echo e($workout->workoutReps); ?>

                                            <?php else: ?>
                                                --
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr data-empty-workout>
                                        <td colspan="3">No workouts scheduled for today.</td>
                                    </tr>
                                <?php endif; ?>
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
                            <p id="account-settings-summary"><?php echo e($profileUser['fullName']); ?> &middot; <?php echo e($profileUser['email']); ?></p>
                        </div>
                        <button class="ghost-button" type="button" data-settings-toggle="account-settings-form" aria-controls="account-settings-form" aria-expanded="false">Edit</button>
                    </div>
                    <form id="account-settings-form" class="settings-form" action="/profile/account" method="POST" hidden novalidate>
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="form-grid">
                            <div class="field">
                                <label for="account-first-name">First name</label>
                                <input id="account-first-name" name="first_name" type="text" value="<?php echo e($profileUser['firstName']); ?>" autocomplete="given-name">
                                <p class="field-error" data-field-error="first_name"></p>
                            </div>
                            <div class="field">
                                <label for="account-last-name">Last name</label>
                                <input id="account-last-name" name="last_name" type="text" value="<?php echo e($profileUser['lastName']); ?>" autocomplete="family-name">
                                <p class="field-error" data-field-error="last_name"></p>
                            </div>
                            <div class="field field-wide">
                                <label for="account-email">Email</label>
                                <input id="account-email" name="email" type="email" value="<?php echo e($profileUser['email']); ?>" autocomplete="email">
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
                            <p id="body-settings-summary"><?php echo e($profileUser['heightDisplay']); ?> &middot; <?php echo e($profileUser['weightDisplay']); ?> &middot; <?php echo e($profileUser['activityLabel']); ?></p>
                        </div>
                        <button class="ghost-button" type="button" data-settings-toggle="body-settings-form" aria-controls="body-settings-form" aria-expanded="false">Edit</button>
                    </div>
                    <form id="body-settings-form" class="settings-form" action="/profile/body-metrics" method="POST" hidden novalidate>
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="form-grid">
                            <div class="field">
                                <label for="body-birthdate">Birthdate</label>
                                <input id="body-birthdate" name="birthdate" type="date" value="<?php echo e($profileUser['birthdate']); ?>" min="1923-01-01" max="2011-01-01">
                                <p class="field-error" data-field-error="birthdate"></p>
                            </div>
                            <div class="field">
                                <label for="body-sex">Sex</label>
                                <select id="body-sex" name="sex">
                                    <option value="" <?php if($profileUser['sex'] === null): echo 'selected'; endif; ?>>Add later</option>
                                    <option value="male" <?php if($profileUser['sex'] === 'male'): echo 'selected'; endif; ?>>Male</option>
                                    <option value="female" <?php if($profileUser['sex'] === 'female'): echo 'selected'; endif; ?>>Female</option>
                                    <option value="prefer_not_to_say" <?php if($profileUser['sex'] === 'prefer_not_to_say'): echo 'selected'; endif; ?>>Prefer not to say</option>
                                </select>
                                <p class="field-error" data-field-error="sex"></p>
                            </div>
                            <div class="field">
                                <label for="body-height">Height</label>
                                <input id="body-height" name="height_value" type="number" value="<?php echo e($profileUser['heightValue']); ?>" min="0" step="0.01" inputmode="decimal">
                                <p class="field-error" data-field-error="height_value"></p>
                            </div>
                            <div class="field">
                                <label for="body-height-unit">Height unit</label>
                                <select id="body-height-unit" name="height_unit">
                                    <option value="cm" <?php if($profileUser['heightUnit'] === 'cm'): echo 'selected'; endif; ?>>cm</option>
                                    <option value="m" <?php if($profileUser['heightUnit'] === 'm'): echo 'selected'; endif; ?>>m</option>
                                    <option value="in" <?php if($profileUser['heightUnit'] === 'in'): echo 'selected'; endif; ?>>in</option>
                                    <option value="ft" <?php if($profileUser['heightUnit'] === 'ft'): echo 'selected'; endif; ?>>ft</option>
                                </select>
                                <p class="field-error" data-field-error="height_unit"></p>
                            </div>
                            <div class="field">
                                <label for="body-weight">Weight</label>
                                <input id="body-weight" name="weight_value" type="number" value="<?php echo e($profileUser['weightValue']); ?>" min="0" step="0.1" inputmode="decimal">
                                <p class="field-error" data-field-error="weight_value"></p>
                            </div>
                            <div class="field">
                                <label for="body-weight-unit">Weight unit</label>
                                <select id="body-weight-unit" name="weight_unit">
                                    <option value="kg" <?php if($profileUser['weightUnit'] === 'kg'): echo 'selected'; endif; ?>>kg</option>
                                    <option value="lb" <?php if($profileUser['weightUnit'] === 'lb'): echo 'selected'; endif; ?>>lb</option>
                                </select>
                                <p class="field-error" data-field-error="weight_unit"></p>
                            </div>
                            <div class="field field-wide">
                                <label for="body-activity">Activity</label>
                                <select id="body-activity" name="activity">
                                    <option value="sedentary" <?php if($profileUser['activity'] === 'sedentary'): echo 'selected'; endif; ?>>Sedentary: little or no exercise</option>
                                    <option value="light" <?php if($profileUser['activity'] === 'light'): echo 'selected'; endif; ?>>Light: exercise 1-3 times/week</option>
                                    <option value="moderate" <?php if($profileUser['activity'] === 'moderate'): echo 'selected'; endif; ?>>Moderate: exercise 3-5 times/week</option>
                                    <option value="active" <?php if($profileUser['activity'] === 'active'): echo 'selected'; endif; ?>>Active: intense exercise 6-7 times/week</option>
                                    <option value="very_active" <?php if($profileUser['activity'] === 'very_active'): echo 'selected'; endif; ?>>Very active: intense daily exercise or physical work</option>
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
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="/index.js"></script>
    <script src="/files/profile.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'GreekGods | Profile'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/profile/show.blade.php ENDPATH**/ ?>