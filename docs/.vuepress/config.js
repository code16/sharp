import * as fs from 'fs';
import * as path from 'path';
import { defineUserConfig } from 'vuepress';
import { docsearchPlugin } from '@vuepress/plugin-docsearch';
import { viteBundler } from '@vuepress/bundler-vite';
import * as dotenv from 'dotenv';
import svgLoader from 'vite-svg-loader';
import sidebar from './sidebar';
import theme from './theme';
import fathomPlugin from './plugins/fathom';
import markdownPlugin from './plugins/markdown';


const demoEnvPath = path.resolve(__dirname, '../../demo/.env');

dotenv.config({
    path: fs.existsSync(demoEnvPath)
        ? demoEnvPath
        : path.resolve(__dirname, '../../saturn/.env'),
});

const {
    APP_URL = 'https://sharp.code16.fr',
    DOCS_TITLE = 'Sharp',
    DOCS_ENABLE_VERSIONING = 'false',
    DOCS_VERSION = '7.0',
    DOCS_VERSION_ITEMS = '[]',
    DOCS_MAIN_URL = APP_URL,
    DOCS_ALGOLIA_TAG = 'v7'
} = process.env;

const DOCS_HOME_URL = DOCS_MAIN_URL === APP_URL ? '/' : DOCS_MAIN_URL;

export default defineUserConfig({
    title: DOCS_TITLE,
    base: '/docs/',
    head: [
        ['link', { rel: 'icon', type:'image/png', href: '/docs/favicon.png' }],
        ['link', { rel: 'icon', type:'image/svg+xml', href: '/docs/favicon.svg' }],
        ['meta', { name: 'og:image', content: '/docs/img/og-image.png' }],
    ],
    theme: theme({
        logo: '/logo.svg',
        navbar: [
            DOCS_ENABLE_VERSIONING === 'true' && {
                text: DOCS_VERSION,
                children: JSON.parse(DOCS_VERSION_ITEMS || '[]')
                    .map(item => ({ ...item, target: '_self' })),
            },
            { text: 'Home', link: DOCS_HOME_URL, target: '_self' },
            { text: 'Documentation', link: '/guide/' },
            { text: 'Demo', link: `${DOCS_MAIN_URL}/sharp/` },
            { text: 'Github', link:'https://github.com/code16/sharp' },
            {
                text: 'Links',
                children: [
                    { text: 'Medium', link:'https://medium.com/code16/tagged/sharp' },
                    { text: 'Discord', link:'https://discord.com/invite/sFBT5c3XZz' },
                ]
            }
        ].filter(Boolean),

        sidebar,
    }),
    markdown: {
        code: {
            lineNumbers: false,
        },
    },
    plugins: [
        docsearchPlugin({
            appId: '1A1N8XRQFM',
            apiKey: 'c5c8c8034f3c0586d562fdbb0a4d26cb',
            indexName: 'code16_sharp',
            searchParameters: {
                facetFilters: [`tags:${DOCS_ALGOLIA_TAG}`],
            },
        }),
        fathomPlugin({
            siteId: 'EELMENOG',
            domains: 'sharp.code16.fr',
        }),
        markdownPlugin,
    ],
    bundler: viteBundler({
        viteOptions: {
            plugins: [svgLoader({ svgo: false })],
        },
    }),
    // scss: {
    //     implementation: require('sass'),
    // },
});
