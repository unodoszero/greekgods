<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/files/login.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <form id="login-form" action="/login" method="POST">
            <?php echo csrf_field(); ?>
            <img src="/graphics/logo/logo.png" onclick="location.href='/'" alt="Logo" title="Click here to redirect to home">
            <p>Login</p>
            <p id="description">Ready to power up your fitness journey? We're excited to see you back-let's keep reaching those goals together!</p>

            <?php echo $__env->make('auth.partials.social-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="auth-divider"><span>or login with email</span></div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="e.g. ada.lovelace@icloud.com" value="<?php echo e(old('email')); ?>" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <div class="error-message-container">
                <?php if(session('status')): ?>
                    <p class="status-message"><?php echo e(session('status')); ?></p>
                <?php endif; ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="error-message"><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <button type="submit">Login</button>
            <hr>
            <p>New to GreekGods? Create an account to start your fitness journey with us! <a href="/register">Register</a></p>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="/files/login.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'GreekGods | Login'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/auth/login.blade.php ENDPATH**/ ?>