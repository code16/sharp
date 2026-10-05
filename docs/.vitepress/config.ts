import { type DefaultTheme, defineConfig, loadEnv, type SiteConfig } from 'vitepress'
import * as path from 'node:path';
import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { transformContent } from "./transform-content";
import versions from '../versions/config.json';
import { sidebar } from "./sidebar";
import llmstxt from "vitepress-plugin-llms";

const env = loadEnv('', path.resolve(__dirname, '../../demo'), ['APP', 'DOCS']);

const {
    APP_URL = 'https://sharp.code16.fr',
} = env;


export default async () => {
    const version = process.env.VERSION
        ? versions.find(v => v.slug === process.env.VERSION)!
        : versions[0];

    return defineConfig({
        lang: 'en-US',
        title: version.title,
        description: 'The Content Management Framework for Laravel.',
        base: process.env.VERSION ? '/docs/'+version.slug : '/docs/',
        srcDir: process.env.VERSION ? path.resolve(__dirname, '../versions/'+version.slug) : process.cwd(),
        outDir: path.resolve(__dirname, './dist/'+version.slug),

        lastUpdated: true,
        cleanUrls: true,

        head: [
            ['link', { rel: 'icon', type: 'image/svg+xml', href: `/docs/${version.slug}/favicon.svg` }],
            ['link', { rel: 'icon', type: 'image/png', href: `/docs/${version.slug}/favicon.png` }],
            ['meta', { name: 'theme-color', content: '#007bff' }],
            ['meta', { name: 'og:type', content: 'website' }],
            ['meta', { name: 'og:locale', content: 'en' }],
            ['meta', { name: 'og:site_name', content: version.title! }],
            ['meta', { name: 'og:image', content: `/docs/${version.slug}/og-image.png` }],
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
            logo: { src: '/logo.svg', width: 100, height: 24, alt: version.title },

            logoLink: APP_URL,

            siteTitle: false,

            nav: nav(),

            outline: {
                level: [2, 3],
            },

            sidebar: {
                '/guide/': process.env.VERSION
                    ? (await import(`../versions/${version.slug}/.vitepress/sidebar.ts`)).sidebar()
                    : sidebar()
            },

            editLink: {
                pattern: `https://github.com/code16/sharp/edit/${version.branch}/docs/:path`,
                text: 'Edit this page on GitHub'
            },

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
                        facetFilters: [`tags:${version.algoliaTag}`],
                    },
                }
            },
        },

        sitemap: {
            hostname: `https://sharp.code16.fr/docs/${version.slug}/`,
        },

        vite: {
            plugins: [llmstxt({ ignoreFiles: ['versions/**'] })],
        },

        buildEnd(siteConfig: SiteConfig) {
            fixLlmstxtPaths(siteConfig);
            if (process.env.UPDATE_LLMSTXT_FOR_VENDOR_PACKAGE) {
                updateLlmstxtForVendorPackage(siteConfig);
            }
        },
    });

    // vitepress-plugin-llms does not prefix the site base on links of nested sidebar groups,
    // so we fix them (e.g. "/guide/form-fields/text.md" => "/docs/10.x/guide/form-fields/text.md")
    function fixLlmstxtPaths(siteConfig: SiteConfig) {
        const llmsTxtPath = path.join(siteConfig.outDir, 'llms.txt');
        if (!existsSync(llmsTxtPath)) {
            return;
        }
        const base = siteConfig.site.base.replace(/\/$/, '');
        writeFileSync(
            llmsTxtPath,
            readFileSync(llmsTxtPath, 'utf8').replace(
                /\]\((\/[^)]*)\)/g,
                (match, href) => href.startsWith(`${base}/`) ? match : `](${base}${href})`,
            ),
        );
    }

    // Copies llms.txt to docs/llms.txt, rewriting its site links ("/docs/guide/x.md") to paths relative to docs/,
    // so that they resolve when the docs are shipped in the composer package (vendor/code16/sharp/docs/).
    function updateLlmstxtForVendorPackage(siteConfig: SiteConfig) {
        const base = siteConfig.site.base.replace(/\/$/, '');
        const content = readFileSync(path.join(siteConfig.outDir, 'llms.txt'), 'utf8')
            .replace(
                /\]\((\/[^)]*)\)/g,
                (match, href) => {
                    if (!href.startsWith(`${base}/`)) {
                        return match;
                    }
                    const relativePath = href.slice(base.length + 1);
                    return `](${relativePath === 'guide.md' ? 'guide/index.md' : relativePath})`;
                },
            );
        writeFileSync(path.resolve(__dirname, '../llms.txt'), content);
    }

    function nav(): DefaultTheme.NavItem[] {
        return [
            { text: 'Documentation', link: '/guide/' },
            {
                text: version.name,
                items: versions
                    .map(version => ({
                        text: version.name,
                        link: version.url ?? `${APP_URL}/docs/${version.slug}/guide/`,
                        target: '_self',
                    })),
            },
            {
                text: 'More',
                items: [
                    { text: 'Demo', link: `${APP_URL}/sharp/` },
                    { text: 'Code 16’s blog', link:'https://code16.fr/blog' },
                ]
            },
        ];
    }
}



