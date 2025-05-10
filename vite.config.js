import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: ['resources/views/**', 'routes/**'],
        }),
    ],
    server: {
        watch: {
            usePolling: true,
            ignored: ['**/vendor/**', '**/storage/**', '**/node_modules/**'],
        },
    },
});
