import { createInertiaApp } from '@inertiajs/react';
import { createRoot, hydrateRoot } from 'react-dom/client';
import type { ComponentType } from 'react';

createInertiaApp({
    title: (title) => (title ? `${title} | اندیشکده` : 'اندیشکده'),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true });
        const page = pages[`./Pages/${name}.tsx`] as { default: ComponentType } | undefined;

        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        return page.default;
    },
    setup({ el, App, props }) {
        if (el.hasChildNodes()) {
            hydrateRoot(el, <App {...props} />);
        } else {
            createRoot(el).render(<App {...props} />);
        }
    },
    progress: { color: '#2f6558' },
});
