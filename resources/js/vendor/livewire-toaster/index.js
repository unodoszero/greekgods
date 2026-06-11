// Vendored from masmerise/livewire-toaster 2.10.0 for builds without Composer's vendor directory.
import { Hub } from './hub';
import * as Toaster from './toaster';

window.Toaster = Toaster;

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(Hub);
});
