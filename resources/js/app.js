import './bootstrap';

import Alpine from 'alpinejs';
import "@hotwired/turbo";

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('turbo:before-render', (event) => {
    const newPage = event.detail.newBody || event.detail.newFrame;
    const oldSearchInputs = document.querySelectorAll('input[type="search"]');
    oldSearchInputs.forEach(input => input.value = '');
    document.body.classList.add('opacity-75', 'transition', 'duration-150');
    setTimeout(() => document.body.classList.remove('opacity-75'), 150);
});

document.addEventListener('turbo:load', () => {
    if (window.Alpine) Alpine.flushAndStopDeferringMutations();
});
