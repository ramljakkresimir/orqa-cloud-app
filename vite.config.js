import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/cloud.css',
                'resources/css/cloud-auth.css',
                'resources/js/cloud.js',
            ],
            refresh: true,
        }),
    ],
});
