import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import ReactDOMServer from 'react-dom/server';
import type { ComponentType } from 'react';

createServer((page) =>
    createInertiaApp({
        page,
        render: ReactDOMServer.renderToString,
        title: (title) => (title ? `${title} | اندیشکده` : 'اندیشکده'),
        resolve: (name) => {
            const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true });
            const resolved = pages[`./Pages/${name}.tsx`] as { default: ComponentType } | undefined;

            if (!resolved) {
                throw new Error(`Inertia page not found: ${name}`);
            }

            return resolved.default;
        },
        setup: ({ App, props }) => <App {...props} />,
    }),
);
