<?php if (! $__env->hasRenderedOnce('f4d9b8f0-88ba-4c62-84ea-d2f1ff5dfc84')): $__env->markAsRenderedOnce('f4d9b8f0-88ba-4c62-84ea-d2f1ff5dfc84'); ?>
    <style>
        .gg-toaster {
            position: fixed;
            inset-inline: 0;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            width: 100%;
            padding: 18px;
            pointer-events: none;
        }

        .gg-toaster.is-bottom {
            bottom: 0;
        }

        .gg-toaster.is-middle {
            top: 50%;
            transform: translateY(-50%);
        }

        .gg-toaster.is-top {
            top: 0;
        }

        .gg-toaster.is-left {
            align-items: flex-start;
        }

        .gg-toaster.is-center {
            align-items: center;
        }

        .gg-toaster.is-right {
            align-items: flex-end;
        }

        .gg-toast {
            position: relative;
            width: min(100%, 340px);
            margin-block: 10px;
            pointer-events: auto;
            transition: opacity 180ms ease, transform 180ms ease;
        }

        .gg-toast__message {
            display: block;
            width: 100%;
            min-height: 48px;
            padding: 13px 46px 13px 16px;
            border: 1px solid var(--gg-color-border);
            border-radius: 8px;
            box-shadow: 0 16px 38px var(--gg-color-shadow-elevated);
            font-family: Trebuchet MS, Arial, sans-serif;
            font-size: 0.9rem;
            font-style: normal;
            font-weight: 800;
            line-height: 1.35;
            color: var(--gg-color-text);
            background: var(--gg-color-surface);
        }

        .gg-toast__message.is-success {
            border-color: var(--gg-color-success-border);
            color: var(--gg-color-success);
            background: var(--gg-color-success-soft);
        }

        .gg-toast__message.is-error {
            border-color: var(--gg-color-danger-border);
            color: var(--gg-color-danger);
            background: var(--gg-color-danger-soft);
        }

        .gg-toast__message.is-warning {
            border-color: var(--gg-color-warning-border);
            color: var(--gg-color-warning);
            background: var(--gg-color-warning-soft);
        }

        .gg-toast__message.is-info {
            border-color: var(--gg-color-action-border);
            color: var(--gg-color-info);
            background: var(--gg-color-action-soft);
        }

        .gg-toast__close {
            position: absolute;
            top: 50%;
            right: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 999px;
            color: currentColor;
            background: transparent;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .gg-toast__close:hover,
        .gg-toast__close:focus-visible {
            background: var(--gg-color-shadow-subtle);
            outline: none;
        }

        .gg-toast__close-icon {
            width: 16px;
            height: 16px;
        }

        .gg-toast.is-center {
            text-align: center;
        }

        .gg-toast-enter-bottom-start,
        .gg-toast-leave-end {
            opacity: 0;
            transform: translateY(18px) scale(0.98);
        }

        .gg-toast-enter-top-start {
            opacity: 0;
            transform: translateY(-18px) scale(0.98);
        }

        .gg-toast-enter-middle-start {
            opacity: 0;
            transform: scale(0.94);
        }

        .gg-toast-enter-end {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        @media (max-width: 640px) {
            .gg-toaster {
                padding: 12px;
            }

            .gg-toast {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .gg-toast {
                transition-duration: 1ms;
            }
        }
    </style>
<?php endif; ?>

<div
    role="status"
    id="toaster"
    x-data="toasterHub(<?php echo \Illuminate\Support\Js::from($toasts)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($config)->toHtml() ?>)"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'gg-toaster',
        'is-bottom' => $alignment->is('bottom'),
        'is-middle' => $alignment->is('middle'),
        'is-top' => $alignment->is('top'),
        'is-left' => $position->is('left'),
        'is-center' => $position->is('center'),
        'is-right' => $position->is('right'),
    ]); ?>"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.isVisible"
            x-init="$nextTick(() => toast.show($el))"
            <?php if($alignment->is('bottom')): ?>
                x-transition:enter-start="gg-toast-enter-bottom-start"
            <?php elseif($alignment->is('top')): ?>
                x-transition:enter-start="gg-toast-enter-top-start"
            <?php else: ?>
                x-transition:enter-start="gg-toast-enter-middle-start"
            <?php endif; ?>
            x-transition:enter-end="gg-toast-enter-end"
            x-transition:leave-end="gg-toast-leave-end"
            class="<?php echo \Illuminate\Support\Arr::toCssClasses(['gg-toast', 'is-center' => $position->is('center')]); ?>"
        >
            <i
                x-text="toast.message"
                class="gg-toast__message"
                :class="toast.select({ error: 'is-error', info: 'is-info', success: 'is-success', warning: 'is-warning' })"
            ></i>

            <?php echo $__env->renderWhen($closeable, 'toaster::close-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
        </div>
    </template>
</div>
<?php /**PATH /Users/angelomiranda/Projects/greekgods/resources/views/vendor/toaster/hub.blade.php ENDPATH**/ ?>