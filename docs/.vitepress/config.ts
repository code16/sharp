import {type DefaultTheme, defineConfig, loadEnv} from 'vitepress'
import path from "path";
import { sidebar } from "./sidebar";
import { transformContent } from "./transform-content";

const env = loadEnv('', path.resolve(__dirname, '../../demo'), ['APP', 'DOCS']);

const {
    APP_URL = 'https://sharp.code16.fr',
    DOCS_TITLE = 'Sharp',
    DOCS_ENABLE_VERSIONING = 'false',
    DOCS_VERSION = '7.0',
    DOCS_VERSION_ITEMS = '[]',
    DOCS_MAIN_URL = APP_URL,
    DOCS_ALGOLIA_TAG = 'v7'
} = env;

const isLastVersion = DOCS_MAIN_URL === APP_URL;
const DOCS_HOME_URL = isLastVersion ? '/docs/' : DOCS_MAIN_URL;

export default defineConfig({
    lang: 'en-US',
    title: DOCS_TITLE,
    description: 'The Content Management Framework for Laravel.',
    base: '/docs/',

    lastUpdated: true,
    cleanUrls: true,

    head: [
        ['link', { rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg' }],
        ['link', { rel: 'icon', type: 'image/png', href: '/favicon.png' }],
        ['meta', { name: 'theme-color', content: '#007bff' }],
        ['meta', { name: 'og:type', content: 'website' }],
        ['meta', { name: 'og:locale', content: 'en' }],
        ['meta', { name: 'og:site_name', content: DOCS_TITLE }],
        ['meta', { name: 'og:image', content: `${APP_URL}/og-image.png` }],
        ['script', { src: 'https://cdn.usefathom.com/script.js', 'data-site': 'EELMENOG', 'data-spa': 'auto', defer: '' }]
    ],

    markdown: {
        config(md) {
            const render = md.render;
            md.render = (...args) => {
                return transformContent(render.call(md, ...args));
            }
        },
    },

    themeConfig: {
        logo: { src: '/logo.svg', width: 120, height: 24, alt: DOCS_TITLE },

        logoLink: DOCS_HOME_URL,

        siteTitle: false,

        nav: nav(),

        outline: {
            level: [2, 3],
        },

        sidebar: {
            '/guide/': sidebar()
        },

        editLink: isLastVersion ? {
            pattern: 'https://github.com/code16/sharp/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        } : undefined,

        socialLinks: [
            { icon: 'github', link: 'https://github.com/code16/sharp' },
            { icon: 'discord', link: 'https://discord.com/invite/sFBT5c3XZz' },
        ],

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2017-present Code16'
        },

        search: {
            provider: 'algolia',
            options: {
                appId: '1A1N8XRQFM',
                apiKey: 'c5c8c8034f3c0586d562fdbb0a4d26cb',
                indexName: 'ode16_sharp',
                searchParameters: {
                    facetFilters: [`tags:${DOCS_ALGOLIA_TAG}`],
                }
            }
        },
    },
});


function nav(): DefaultTheme.NavItem[] {
    return [
        { text: 'Documentation', link: '/guide/' },
        ...DOCS_ENABLE_VERSIONING === 'true' ? [
            {
                text: DOCS_VERSION,
                items: JSON.parse(DOCS_VERSION_ITEMS || '[]')
                    .map(item => ({
                        text: item.text,
                        link: item.link,
                        target: '_self',
                    })),
            }
        ] : [],
        {
            text: 'More',
            items: [
                { text: 'Demo', link: `${DOCS_MAIN_URL}/sharp/` },
                { text: 'Code 16’s blog', link:'https://code16.fr/blog' },
            ]
        },
    ];
}
