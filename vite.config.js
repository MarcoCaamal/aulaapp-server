import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/drop-zone.css',
                'resources/js/app.js',
                'resources/js/alerts.js',
                'resources/js/dashboard-charts.js',
                'resources/js/date-pickers.js',
                'resources/js/drop-zone.js',
            ],
            refresh: true,
        }),
    ],
});
