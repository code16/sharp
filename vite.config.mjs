import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from 'path';
import ignoreImport from 'rollup-plugin-ignore-import';
import { splitVendorChunkPlugin } from 'vite';
import { warmup } from 'vite-plugin-warmup';

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
            hmr: false,
        },
        plugins: [
            warmup({
                clientFiles: [
                    './resources/js/Pages/**/*.vue',
                ],
            }),
            splitVendorChunkPlugin(),
            laravel({
                input: [
                    'resources/js/sharp.ts',
                    'resources/sass/app.css',
                    // 'resources/sass/app.scss',
                    'resources/sass/vendors.scss',
                ],
                publicDirectory: '/dist',
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
            // watch({
            //     pattern: "src/{Data,Enums}/**/*.php",
            //     command: "composer typescript:transform",
            // }),
            ignoreImport({
                include: [
                    /moment\/locale\/(?!fr\.js$).*\.js$/,
                    /bootstrap-vue\/esm\/(icons\/icons)/,
                ],
                // exclude: [
                //
                // ],
            }),
            ...(command === 'build' ? [
                babel({
                    babelHelpers: 'bundled',
                    exclude: [
                        "node_modules/**",
                        /type=style/, // ignore .vue outputs with styles
                    ],
                    extensions: ['.js', '.vue'],
                })
            ] : []),
        ],
    }
});
