import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from 'path';
import ignoreImport from 'rollup-plugin-ignore-import';
import legacy from '@vitejs/plugin-legacy'
import svgLoader from 'vite-svg-loader';
import circleDependency from 'vite-plugin-circular-dependency';

export default defineConfig(({ mode, command }) => {
    const env = loadEnv(mode, path.join(process.cwd(), '/demo'), '');
    return {
        base: (command === "build" ? '/vendor/sharp' : ""),
        envDir: path.join(process.cwd(), '/demo'),
        build: {
            outDir: 'dist',
            commonjsOptions: {
                requireReturnsDefault: 'preferred'
            },
        },
        resolve: {
            alias: {
                'vue': 'vue/dist/vue.esm-browser.js',
            }
        },
        server: {
            // hmr: false,
            warmup: {
                clientFiles: [
                    './resources/js/Pages/**/*.vue',
                    // './resources/css/app.css',
                ],
            },
        },
        plugins: [
            // circleDependency(),
            svgLoader({ svgo: false }),
            laravel({
                input: [
                    'resources/js/sharp.ts',
                    // 'resources/css/sharp.css',
                    // 'resources/css/vendors.css',
                ],
                publicDirectory: '/dist',
                refresh: [
                    'demo/app/Sharp/**/*.php',
                ],
                // refresh: true,
                detectTls: env.APP_URL?.startsWith('https')
                    ? env.APP_URL.replace('https://', '')
                    : false,
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
                ],
            }),
            ...command === 'build' ? [
                legacy({
                    modernPolyfills: ['es/array/to-spliced'],
                    renderLegacyChunks: false,
                }),
            ] : [],
        ],
    }
});
