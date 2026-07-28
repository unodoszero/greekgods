<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" type="image/png" href="/graphics/logo/logo.png">
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/palette.css'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <title><?php echo e($title ?? 'GreekGods'); ?></title>
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>
    <?php if (isset($component)) { $__componentOriginalafb36adf865af54d1e1d61c1adc535d1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalafb36adf865af54d1e1d61c1adc535d1 = $attributes; } ?>
<?php $component = Masmerise\Toaster\ToasterHub::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toaster-hub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Masmerise\Toaster\ToasterHub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalafb36adf865af54d1e1d61c1adc535d1)): ?>
<?php $attributes = $__attributesOriginalafb36adf865af54d1e1d61c1adc535d1; ?>
<?php unset($__attributesOriginalafb36adf865af54d1e1d61c1adc535d1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalafb36adf865af54d1e1d61c1adc535d1)): ?>
<?php $component = $__componentOriginalafb36adf865af54d1e1d61c1adc535d1; ?>
<?php unset($__componentOriginalafb36adf865af54d1e1d61c1adc535d1); ?>
<?php endif; ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/layouts/app.blade.php ENDPATH**/ ?>