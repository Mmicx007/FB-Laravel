import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            entries: {
                'app': 'resources/js/app.js',
            },
            css: [
                'resources/css/app.css',
                'resources/css/login.css',
            ],
        }),
    ],
});
