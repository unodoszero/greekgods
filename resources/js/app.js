import '../../vendor/masmerise/livewire-toaster/resources/js';

const dispatchToast = (type, message) => {
    if (!message || typeof window.Toaster?.[type] !== 'function') {
        return;
    }

    window.Toaster[type](message);
};

window.GreekGodsToast = {
    error: (message) => dispatchToast('error', message),
    info: (message) => dispatchToast('info', message),
    success: (message) => dispatchToast('success', message),
    warning: (message) => dispatchToast('warning', message),
};

const confirmStyles = `
    .gg-confirm {
        position: fixed;
        inset: 0;
        z-index: 9998;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(24, 24, 21, 0.48);
    }

    .gg-confirm[hidden] {
        display: none;
    }

    .gg-confirm__dialog {
        width: min(420px, 100%);
        border: 1px solid #e6e0d5;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 24px 70px rgba(24, 24, 21, 0.28);
        font-family: Trebuchet MS, Arial, sans-serif;
        overflow: hidden;
    }

    .gg-confirm__body {
        padding: 22px 22px 18px;
    }

    .gg-confirm__title {
        margin: 0 0 10px;
        color: #191918;
        font-size: 1.1rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .gg-confirm__message {
        margin: 0;
        color: #5f5c55;
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.45;
    }

    .gg-confirm__actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid #ebe6de;
        background: #faf8f4;
    }

    .gg-confirm__button {
        min-width: 96px;
        min-height: 42px;
        border: 0;
        border-radius: 6px;
        padding: 0 16px;
        font: inherit;
        font-size: 0.84rem;
        font-weight: 900;
        letter-spacing: 0;
        cursor: pointer;
    }

    .gg-confirm__button--cancel {
        color: #252421;
        background: #ece7df;
    }

    .gg-confirm__button--confirm {
        color: #ffffff;
        background: #245f3a;
    }

    .gg-confirm__button--danger {
        background: #8f2c22;
    }

    .gg-confirm__button:focus-visible {
        outline: 3px solid rgba(36, 95, 58, 0.25);
        outline-offset: 2px;
    }
`;

const ensureConfirmStyles = () => {
    if (document.getElementById('gg-confirm-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'gg-confirm-styles';
    style.textContent = confirmStyles;
    document.head.appendChild(style);
};

const createConfirmModal = () => {
    ensureConfirmStyles();

    const backdrop = document.createElement('div');
    backdrop.className = 'gg-confirm';
    backdrop.hidden = true;
    backdrop.innerHTML = `
        <div class="gg-confirm__dialog" role="dialog" aria-modal="true" aria-labelledby="gg-confirm-title" aria-describedby="gg-confirm-message">
            <div class="gg-confirm__body">
                <h2 class="gg-confirm__title" id="gg-confirm-title"></h2>
                <p class="gg-confirm__message" id="gg-confirm-message"></p>
            </div>
            <div class="gg-confirm__actions">
                <button class="gg-confirm__button gg-confirm__button--cancel" type="button" data-gg-confirm-cancel></button>
                <button class="gg-confirm__button gg-confirm__button--confirm" type="button" data-gg-confirm-accept></button>
            </div>
        </div>
    `;
    document.body.appendChild(backdrop);

    return backdrop;
};

window.GreekGodsConfirm = (message, options = {}) => new Promise((resolve) => {
    if (typeof document === 'undefined') {
        resolve(false);
        return;
    }

    const modal = createConfirmModal();
    const previousFocus = document.activeElement instanceof HTMLElement ? document.activeElement : null;
    const title = modal.querySelector('#gg-confirm-title');
    const messageNode = modal.querySelector('#gg-confirm-message');
    const cancelButton = modal.querySelector('[data-gg-confirm-cancel]');
    const confirmButton = modal.querySelector('[data-gg-confirm-accept]');

    title.textContent = options.title || 'Confirm action';
    messageNode.textContent = message;
    cancelButton.textContent = options.cancelLabel || 'Cancel';
    confirmButton.textContent = options.confirmLabel || 'Confirm';
    confirmButton.classList.toggle('gg-confirm__button--danger', options.danger !== false);

    const cleanup = (confirmed) => {
        document.removeEventListener('keydown', onKeydown);
        modal.remove();
        previousFocus?.focus();
        resolve(confirmed);
    };

    const onKeydown = (event) => {
        if (event.key === 'Escape') {
            cleanup(false);
        }
    };

    cancelButton.addEventListener('click', () => cleanup(false));
    confirmButton.addEventListener('click', () => cleanup(true));
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            cleanup(false);
        }
    });
    document.addEventListener('keydown', onKeydown);

    modal.hidden = false;
    confirmButton.focus();
});
