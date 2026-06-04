<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/index.css">
    <link rel="stylesheet" href="/files/program.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <script>
        const userId = <?php echo json_encode($user->id, 15, 512) ?>;
        const userFullName = <?php echo json_encode(trim($user->first_name.' '.$user->last_name), 15, 512) ?>;
        const splitCatalog = <?php echo json_encode($splitCatalog, 15, 512) ?>;
        const workoutData = <?php echo json_encode($workouts, 15, 512) ?>;
        const programState = <?php echo json_encode(['program' => $programState, 'workouts' => $workouts], 512) ?>;
    </script>

    <nav>
        <button class="nav-menu-button" id="nav-menu-button"><img src="/graphics/svg/menu-black.svg" alt="Menu" title="Menu"></button>
        <div class="nav-logo"><img src="/graphics/logo/greekgodslogo.png" alt="GreekGods" title="GreekGods" onclick="window.location.href='/'"></div>
        <button class="nav-menu-profile" id="nav-menu-profile" onclick="window.location.href='/profile'"><img src="/graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
        <ul class="nav-links" id="nav-links">
            <li><a href="/">HOME</a></li>
            <li><a href="/program">PROGRAM</a></li>
            <li><a href="/blog">BLOG</a></li>
            <li><a href="/calculator">CALCULATOR</a></li>
            <li><a href="/about">ABOUT</a></li>
        </ul>
        <div class="nav-button">
            <button id="profile-button" style="display: inline-flex;" aria-label="Open profile" onclick="window.location.href='/profile'"><img src="/graphics/svg/profile.svg" alt="Profile" title="Profile"></button>
            <span id="profile-name" style="display: inline;"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></span>
        </div>
    </nav>

    <header class="program-hero">
        <div class="header-container">
            <div class="header-sections">
                <p class="eyebrow">Program builder</p>
                <h1 id="header-welcome-message">HI, <?php echo e($user->first_name); ?>!</h1>
                <p>Choose a split, save the schedule, then build each training day.</p>
            </div>
        </div>
    </header>

    <section class="program-builder" aria-labelledby="program-builder-title">
        <div class="builder-heading">
            <div>
                <p class="eyebrow">Step 1</p>
                <h2 id="program-builder-title">Choose your split</h2>
            </div>
            <p id="program-status" class="program-status" role="status"></p>
        </div>

        <div class="split-grid" id="split-options" aria-label="Workout splits"></div>

        <div class="schedule-panel">
            <div class="schedule-controls">
                <label for="schedule-options">Step 2: Choose schedule</label>
                <select id="schedule-options" disabled>
                    <option value="">Select a split first</option>
                </select>
            </div>
            <div class="schedule-actions">
                <button id="save-program" type="button">SAVE PROGRAM</button>
                <button id="change-program" type="button">CHANGE PROGRAM</button>
            </div>
        </div>

        <div class="schedule-preview" id="schedule-preview" aria-label="Weekly schedule preview"></div>
    </section>

    <main class="program-workspace" aria-labelledby="weekly-board-title">
        <div class="workspace-heading">
            <div>
                <p class="eyebrow">Step 3</p>
                <h2 id="weekly-board-title">Weekly workout board</h2>
            </div>
            <p id="board-empty-state">Save a split to start adding workouts.</p>
        </div>

        <div class="main-container" id="weekly-board">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="main-days" id="<?php echo e($day); ?>" data-day="<?php echo e($day); ?>">
                    <div class="day-card-header">
                        <div>
                            <h3><?php echo e($day); ?></h3>
                            <p class="split-name">Choose a schedule</p>
                        </div>
                        <span class="workout-count">0</span>
                    </div>
                    <div class="workouts"></div>
                    <p class="day-empty">Save a split to start adding workouts.</p>
                    <button class="add" type="button" disabled>ADD WORKOUT</button>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </main>

    <div class="workout-modal" id="workout-modal" hidden>
        <form class="add-workout" id="workout-form">
            <div class="modal-heading">
                <div>
                    <p class="eyebrow" id="workout-form-day">Training day</p>
                    <h2 id="workout-form-title">Add workout</h2>
                </div>
                <button id="workout-form-cancel" class="icon-button" type="button" aria-label="Close workout form">X</button>
            </div>
            <input type="hidden" id="workout-id">
            <input type="hidden" id="workout-day">
            <label for="workout-name">Workout name</label>
            <input type="text" id="workout-name" placeholder="Bench Press" autocomplete="off">
            <div class="autocomplete-suggestions"></div>
            <div class="form-row">
                <div>
                    <label for="workout-sets">Sets</label>
                    <input type="number" min="1" max="50" id="workout-sets" placeholder="3">
                </div>
                <div>
                    <label for="workout-reps">Reps</label>
                    <input type="number" min="1" max="100" id="workout-reps" placeholder="10">
                </div>
            </div>
            <button id="form-add" type="submit">SAVE WORKOUT</button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="/index.js"></script>
    <script src="/files/program.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'GreekGods | Program'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/program/show.blade.php ENDPATH**/ ?>