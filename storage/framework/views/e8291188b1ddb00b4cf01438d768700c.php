<?php
    $socialProviders = \App\Support\SocialAuthProviders::viewModels();
?>

<div class="social-auth" aria-label="Social authentication options">
    <?php $__currentLoopData = $socialProviders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider => $socialProvider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($socialProvider['configured']): ?>
            <a class="social-auth-button social-auth-button-<?php echo e($provider); ?>" href="<?php echo e(route('social.redirect', ['provider' => $provider])); ?>" aria-label="Continue with <?php echo e($socialProvider['label']); ?>">
                <?php echo $__env->make('auth.partials.social-mark', ['provider' => $provider], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span>Continue with <?php echo e($socialProvider['label']); ?></span>
            </a>
        <?php else: ?>
            <span class="social-auth-button social-auth-button-<?php echo e($provider); ?> social-auth-button-disabled" aria-disabled="true" title="<?php echo e($socialProvider['label']); ?> sign-in is not configured yet.">
                <?php echo $__env->make('auth.partials.social-mark', ['provider' => $provider], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <span>Continue with <?php echo e($socialProvider['label']); ?></span>
            </span>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/auth/partials/social-buttons.blade.php ENDPATH**/ ?>