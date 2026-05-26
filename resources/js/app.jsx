import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const el = document.getElementById('app');
const page = el && el.dataset.page ? JSON.parse(el.dataset.page) : undefined;

createInertiaApp({
    page,
    title: (title) => `${title} — Sistem BHP Lab`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el: mountEl, App, props }) {
        createRoot(mountEl || el).render(<App {...props} />);
    },
    progress: {
        color: '#5E6AD2',
        showSpinner: false,
    },
});

