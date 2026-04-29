import { type DefaultTheme, defineConfig, loadEnv } from 'vitepress'
import * as path from "path";
import { transformContent } from "./transform-content";
import versions from "../versions.json";

const env = loadEnv('', path.resolve(__dirname, '../../demo'), ['APP', 'DOCS']);

const {
    APP_URL = 'https://sharp.code16.fr',
    DOCS_MAIN_URL = APP_URL,
    DOCS_ALGOLIA_TAG = 'v7'
} = env;

const isLastVersion = DOCS_MAIN_URL === APP_URL;
const DOCS_HOME_URL = isLastVersion ? '/docs/' : DOCS_MAIN_URL;

export default async () => {
    const sidebar = Object.fromEntries(
        await Promise.all(versions.filter(version => version.slug).map(async (version) => {
            return [
                `/${version.slug}/`,
                {
                    items: (await import(`../versions/${version.slug}/.vitepress/sidebar.ts`)).sidebar(),
                    base: `/${version.slug}/`
                }
            ];
        }))
    );

    return defineConfig({
        lang: 'en-US',
        title: 'Sharp',
        description: 'The Content Management Framework for Laravel.',
        base: '/docs/',

        lastUpdated: true,
        cleanUrls: true,

        head: [
            ['link', { rel: 'icon', type: 'image/svg+xml', href: '/docs/favicon.svg' }],
            ['link', { rel: 'icon', type: 'image/png', href: '/docs/favicon.png' }],
            ['meta', { name: 'theme-color', content: '#007bff' }],
            ['meta', { name: 'og:type', content: 'website' }],
            ['meta', { name: 'og:locale', content: 'en' }],
            ['meta', { name: 'og:site_name', content: 'Sharp' }],
            ['meta', { name: 'og:image', content: `${APP_URL}/og-image.png` }],
            ['script', { src: 'https://cdn.usefathom.com/script.js', 'data-site': 'EELMENOG', 'data-spa': 'auto', defer: '' }]
        ],

        rewrites(id) {
            return id.replace(/^versions\/([^/]+)/, '$1');
        },

        markdown: {
            config(md) {
                const render = md.render;
                md.render = (...args) => {
                    return transformContent(render.call(md, ...args));
                }
            },
        },

        themeConfig: {
            logo: { src: '/logo.svg', width: 100, height: 24, alt: 'Sharp' },

            logoLink: DOCS_HOME_URL,

            siteTitle: false,

            nav: nav(),

            outline: {
                level: [2, 3],
            },

            sidebar: sidebar,

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
                    indexName: 'code16_sharp',
                    searchParameters: {
                        facetFilters: [`tags:${DOCS_ALGOLIA_TAG}`],
                    },
                }
            },
        },
    });
}


function nav(): DefaultTheme.NavItem[] {
    return [
        {
            component: 'VersionNavMenu',
            props: {
                items: versions.map(version => ({
                    text: version.name,
                    link: version.url || `/${version.slug}/guide/`,
                })),
            },
        },
        {
            text: 'More',
            items: [
                { text: 'Demo', link: `/../sharp/` },
                { text: 'Code 16’s blog', link:'https://code16.fr/blog' },
            ]
        },
    ];
}
