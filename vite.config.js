import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import * as path from 'path';
import ignoreImport from 'rollup-plugin-ignore-import';
import { splitVendorChunkPlugin } from 'vite';

export default defineConfig(({ mode, command }) => {
    const env = loadEnv(mode, path.join(process.cwd(), '/demo'), '');
    return {
        base: (command === "build" ? '/vendor/sharp' : ""),
        envDir: path.join(process.cwd(), '/demo'),
        build: {
            outDir: 'resources/assets/dist',
            commonjsOptions: {
                requireReturnsDefault: 'preferred'
            },
        },
        resolve: {
            alias: {
                'vue': 'vue/dist/vue.esm.js',
            }
        },
        plugins: [
            splitVendorChunkPlugin(),
            laravel({
                input: [
                    'resources/assets/js/sharp.js',
                    'resources/assets/js/client-api.js',
                    'resources/assets/sass/app.scss',
                    'resources/assets/sass/vendors.scss',
                ],
                publicDirectory: 'resources/assets/dist',
                refresh: true,
                detectTls: env.APP_URL?.startsWith('https')
                    ? env.APP_URL.replace('https://', '')
                    : null,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            ignoreImport({
                include: [
                    /moment\/locale\/(?!fr\.js$).*\.js$/,
                    /bootstrap-vue\/esm\/(icons\/icons)/,
                ],
                // exclude: [
                //
                // ],
            }),
        ],
    }
});
