import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/sidebar-toggle.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        // Vite serve apenas assets/HMR na porta 5173.
        // O Laravel (php artisan serve) corre independentemente na porta 8001.
        // O laravel-vite-plugin gere a integração via public/hot automaticamente.
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        cssMinify: true,
    }
});
