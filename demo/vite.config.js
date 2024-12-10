import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/sharp-plugin.js',
            'resources/css/sharp-extension.css',
        ]),
    ],
});
