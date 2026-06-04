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
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?><?php /**PATH /Users/angelomiranda/Projects/greekgods/storage/framework/views/829e7c836dbf3d67b5e9f3200c500ae2.blade.php ENDPATH**/ ?>