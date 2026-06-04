<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/files/register.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <form class="container" action="/register" method="POST" id="registrationForm">
        <?php echo csrf_field(); ?>
        <div id="registerAccount">
            <img src="/graphics/logo/logo.png" alt="Register">
            <p id="register">Register</p>
            <p id="description">Start strong with GreekGods, every journey begins here!</p>

            <?php echo $__env->make('auth.partials.social-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="auth-divider"><span>or create an account with email</span></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. ada.lovelace@icloud.com" value="<?php echo e(old('email')); ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="password_confirmation" required>

            <div class="terms">
                <input type="checkbox" id="check" name="check" value="1" required>
                <label for="check">
                    I agree to the GreekGods <a href="/laws" target="_blank">Terms of Service</a>
                    and acknowledge the <a href="/laws" target="_blank">Privacy Policy</a>.
                </label>
            </div>

            <div class="error-message-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="error-message"><?php echo e($error); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <button type="button" id="registerButton">Agree and Start Now</button>
            <hr>
            <p>Already have an account? <a href="/login">Login</a></p>
        </div>
        <div id="registerInfo">
            <p id="register">Register</p>
            <p id="description">Start strong with GreekGods, every journey begins here!</p>

            <div class="section">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first_name" placeholder="e.g. Ada" value="<?php echo e(old('first_name')); ?>" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last_name" placeholder="e.g. Lovelace" value="<?php echo e(old('last_name')); ?>" required>

                <label for="birthdate">Birthdate</label>
                <input type="date" id="birthdate" name="birthdate" min="1923-01-01" max="2011-01-01" value="<?php echo e(old('birthdate')); ?>" required>

                <label for="sex">Sex</label>
                <select name="sex" id="sex" required>
                    <option value="" disabled <?php if(old('sex') === null): echo 'selected'; endif; ?>>Select sex</option>
                    <option value="male" <?php if(old('sex') === 'male'): echo 'selected'; endif; ?>>Male</option>
                    <option value="female" <?php if(old('sex') === 'female'): echo 'selected'; endif; ?>>Female</option>
                    <option value="prefer_not_to_say" <?php if(old('sex') === 'prefer_not_to_say'): echo 'selected'; endif; ?>>Prefer not to say</option>
                </select>
            </div>

            <label for="height">Height</label>
            <div class="section-metrics">
                <input id="height" type="number" name="height_value" placeholder="e.g. 5.7" step="0.01" value="<?php echo e(old('height_value')); ?>" required>
                <select name="height_unit" id="heightMetric" required>
                    <option value="cm" <?php if(old('height_unit', 'cm') === 'cm'): echo 'selected'; endif; ?>>.cm</option>
                    <option value="in" <?php if(old('height_unit') === 'in'): echo 'selected'; endif; ?>>.in</option>
                    <option value="m" <?php if(old('height_unit') === 'm'): echo 'selected'; endif; ?>>.m</option>
                    <option value="ft" <?php if(old('height_unit') === 'ft'): echo 'selected'; endif; ?>>.ft</option>
                </select>
            </div>
            <p class="input-hint">For ft, type 5.7 for 5 ft 7 in.</p>

            <label for="weight">Weight</label>
            <div class="section-metrics">
                <input id="weight" type="number" name="weight_value" placeholder="e.g. 75" step="0.01" value="<?php echo e(old('weight_value')); ?>" required>
                <select name="weight_unit" id="weightMetric" required>
                    <option value="kg" <?php if(old('weight_unit', 'kg') === 'kg'): echo 'selected'; endif; ?>>.kg</option>
                    <option value="lb" <?php if(old('weight_unit') === 'lb'): echo 'selected'; endif; ?>>.lb</option>
                </select>
            </div>

            <div class="section">
                <label for="activity" data-target="#bmr-activity">Activity<span src="/graphics/svg/info-black.svg"></span></label>
                <select name="activity" id="activity" required>
                    <option value="" disabled selected></option>
                    <option value="sedentary" <?php if(old('activity') === 'sedentary'): echo 'selected'; endif; ?>>Sedentary: little or no exercise</option>
                    <option value="light" <?php if(old('activity') === 'light'): echo 'selected'; endif; ?>>Light: exercise 1-3 times/week</option>
                    <option value="moderate" <?php if(old('activity') === 'moderate'): echo 'selected'; endif; ?>>Moderate: exercise 3-5 times/week</option>
                    <option value="active" <?php if(old('activity') === 'active'): echo 'selected'; endif; ?>>Active: intense exercise 6-7 times/week</option>
                    <option value="very_active" <?php if(old('activity') === 'very_active'): echo 'selected'; endif; ?>>Very Active: very intense exercise daily, or physical job</option>
                </select>
            </div>
            <hr>
            <button type="submit" id="startJourneyNow">Start Journey Now</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="/files/register.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'GreekGods | Register'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/auth/register.blade.php ENDPATH**/ ?>