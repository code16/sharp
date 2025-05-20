import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import * as path from 'node:path';
import legacy from '@vitejs/plugin-legacy'
import svgLoader from 'vite-svg-loader';
import tailwindcss from "@tailwindcss/vite";


export default defineConfig(({ mode, command }) => {
    const env = loadEnv(mode, path.join(process.cwd(), '/demo'), '');
    const host = env.APP_URL ? new URL(env.APP_URL).hostname : null;
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
                'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy'),
                // ...rekaAliases()
            }
        },
        server: {
            // hmr: false,
            hmr: {
                host,
            },
            host,
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
            tailwindcss(),
            laravel({
                input: [
                    'resources/js/sharp.ts',
                    // 'resources/css/sharp.css',
                    // 'resources/css/vendors.css',
                ],
                publicDirectory: '/dist',
                refresh: [
                    // 'demo/app/Sharp/**/*.php',
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
            ...command === 'build' ? [
                legacy({
                    modernPolyfills: ['es/array/to-spliced'],
                    renderLegacyChunks: false,
                }),
            ] : [],
        ],
    }
});

// dev only method
function rekaAliases()
{
    return {
        'reka-ui': path.resolve(__dirname, '../reka-ui/packages/core/src'),
        ...Object.fromEntries([
            'Accordion',
            'AlertDialog',
            'AspectRatio',
            'Avatar',
            'Calendar',
            'Checkbox',
            'Collapsible',
            'Collection',
            'Combobox',
            'ConfigProvider',
            'ContextMenu',
            'date',
            'DateField',
            'DatePicker',
            'DateRangeField',
            'DateRangePicker',
            'Dialog',
            'DismissableLayer',
            'DropdownMenu',
            'Editable',
            'FocusGuards',
            'FocusScope',
            'HoverCard',
            'Label',
            'Listbox',
            'Menu',
            'Menubar',
            'NavigationMenu',
            'NumberField',
            'Pagination',
            'PinInput',
            'Popover',
            'Popper',
            'Presence',
            'Primitive',
            'Progress',
            'RadioGroup',
            'RangeCalendar',
            'RovingFocus',
            'ScrollArea',
            'Select',
            'Separator',
            'shared',
            'Slider',
            'Splitter',
            'Stepper',
            'Switch',
            'Tabs',
            'TagsInput',
            'Teleport',
            'test',
            'TimeField',
            'Toast',
            'Toggle',
            'ToggleGroup',
            'Toolbar',
            'Tooltip',
            'Tree',
            'Viewport',
            'VisuallyHidden'
        ].map(c => [`@/${c}`, path.resolve(__dirname, `../reka-ui/packages/core/src/${c}`)])),
    }
}
