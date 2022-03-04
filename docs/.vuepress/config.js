const fs = require('fs');
const path = require('path');
const Prism = require('prismjs');
const loadLanguages = require('prismjs/components/');

loadLanguages(['php']);

const demoEnvPath = path.resolve(__dirname, '../../demo/.env');

require('dotenv').config({
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

module.exports = {
    title: DOCS_TITLE,
    base: '/docs/',
    head: [
        ['link', { rel: 'icon', type:'image/png', href: '/docs/favicon.png' }],
        ['link', { rel: 'icon', type:'image/svg+xml', href: '/docs/favicon.svg' }],
    ],
    theme: path.resolve(__dirname, './theme'),
    themeConfig: {
        logo: '/logo.svg',
        logoDark: '/logo-dark.svg',
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
        sidebar: [
            {
                text: 'Introduction',
                children: [
                    'README.md',
                    'authentication.md',
                ].map(page => `/guide/${page}`),
            },
            {
                text: 'Entity Lists',
                children: [
                    'building-entity-list.md',
                    'filters.md',
                    'commands.md',
                    'entity-states.md',
                    'reordering-instances.md',
                ].map(page => `/guide/${page}`),
            },
            {
                text: 'Entity Forms',
                children: [
                    'building-entity-form.md',
                    'multiforms.md',
                    'single-form.md',
                    'custom-form-fields.md'
                ].map(page => `/guide/${page}`),
            },
            {
                text: 'Entity Shows',
                children: [
                    'building-entity-show.md',
                    'single-show.md',
                    'custom-show-fields.md'
                ].map(page => `/guide/${page}`),
            },
            {
                text: 'Dashboards',
                children: [
                    '/guide/dashboard.md',
                    ...[
                        'graph.md',
                        'panel.md',
                        'ordered-list.md',
                    ].map(page => `/guide/dashboard-widgets/${page}`),
                ],
            },
            {
                text: 'Generalities',
                children: [
                    'building-menu.md',
                    'sharp-breadcrumb.md',
                    'entity-authorizations.md',
                    'how-to-transform-data.md',
                    'link-to.md',
                    'context.md',
                    'sharp-built-in-solution-for-uploads.md',
                    'form-data-localization.md',
                    'testing-with-sharp.md',
                    'artisan-generators.md',
                    'style-visual-theme.md'
                ].map(page => `/guide/${page}`),
            },
            {
                text: 'Form fields',
                children: [
                    'text.md',
                    'textarea.md',
                    'markdown.md',
                    'wysiwyg.md',
                    'number.md',
                    'html.md',
                    'check.md',
                    'date.md',
                    'upload.md',
                    'select.md',
                    'autocomplete.md',
                    'tags.md',
                    'list.md',
                    'autocomplete-list.md',
                    'geolocation.md',
                ].map(page => `/guide/form-fields/${page}`),
            },
            {
                text: 'Show fields',
                children: [
                    'text.md',
                    'picture.md',
                    'list.md',
                    'file.md',
                    'embedded-entity-list.md',
                ].map(page => `/guide/show-fields/${page}`),
            },
            {
                text: 'Migrations guide',
                children: [
                    '6.0.md',
                    '5.0.md',
                    '4.2.md',
                    '4.1.3.md',
                    '4.1.md',
                ].map(page => `/guide/upgrading/${page}`),
            },
        ],
    },
    plugins: [
        [
            '@vuepress/plugin-docsearch',
            {
                appId: '1A1N8XRQFM',
                apiKey: 'c5c8c8034f3c0586d562fdbb0a4d26cb',
                indexName: 'code16_sharp',
                searchParameters: {
                    facetFilters: [`tags:${DOCS_ALGOLIA_TAG}`],
                },
            }
        ],
        {
            extendsMarkdown: md => {
                md.renderer.rules['code_inline'] = (tokens, idx, options, env, slf) => {
                    let token = tokens[idx];
                    return '<code class="inline">' +
                        Prism.highlight(token.content, Prism.languages.php) +
                        '</code>';
                };
            }
        },
    ],
    bundler: '@vuepress/bundler-vite',
    bundlerConfig: {
        viteOptions: {
            plugins: [require('vite-svg-loader')({ svgo: false })],
        },
    },
    // scss: {
    //     implementation: require('sass'),
    // },
};
