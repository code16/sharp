const path = require('path');
const Prism = require('prismjs');
const loadLanguages = require('prismjs/components/');

loadLanguages(['php']);

require('dotenv').config({
    path: path.resolve(__dirname, '../../saturn/.env'),
});

const {
    APP_NAME = 'Sharp',
    APP_URL = 'https://sharp.code16.fr',
    DOCS_ENABLE_VERSIONING = 'false',
    DOCS_VERSION = '7.0',
    DOCS_VERSION_ITEMS = '[]',
    DOCS_MAIN_URL = APP_URL,
    DOCS_ALGOLIA_TAG = 'v7'
} = process.env;

const DOCS_HOME_URL = DOCS_MAIN_URL === APP_URL ? '/' : DOCS_MAIN_URL;

module.exports = {
    title: APP_NAME,
    base: '/docs/',
    head: [
        ['link', { rel: 'icon', type:'image/png', href: '/favicon.png' }],
        ['link', { rel: 'icon', type:'image/svg+xml', href: '/favicon.svg' }],
    ],
    themeConfig: {
        nav: [
            DOCS_ENABLE_VERSIONING === 'true' && {
                text: DOCS_VERSION,
                items: JSON.parse(DOCS_VERSION_ITEMS || '[]')
                    .map(item => ({ ...item, target: '_self' })),
            },
            { text: 'Home', link: DOCS_HOME_URL, target: '_self' },
            { text: 'Documentation', link: '/guide/' },
            { text: 'Demo', link: `${DOCS_MAIN_URL}/sharp/` },
            { text: 'Github', link:'https://github.com/code16/sharp' },
            {
                text: 'Links',
                items: [
                    { text: 'Medium', link:'https://medium.com/code16/tagged/sharp' },
                    { text: 'Discord', link:'https://discord.com/invite/sFBT5c3XZz' },
                ]
            }
        ].filter(Boolean),
        sidebar: {
            '/guide/': [
                {
                    title: 'Introduction',
                    collapsable: false,
                    children: [
                        '',
                        'authentication',
                    ]
                },
                {
                    title: 'Entity Lists',
                    collapsable: false,
                    children: [
                        'building-entity-list',
                        'filters',
                        'commands',
                        'entity-states',
                        'reordering-instances',
                    ]
                },
                {
                    title: 'Forms',
                    collapsable: false,
                    children: [
                        'building-form',
                        'multiforms',
                        'single-form',
                        'custom-form-fields'
                    ]
                },
                {
                    title: 'Show Pages',
                    collapsable: false,
                    children: [
                        'building-show-page',
                        'single-show',
                        'custom-show-fields'
                    ]
                },
                {
                    title: 'Dashboards',
                    collapsable: false,
                    children: [
                        'building-dashboard',
                        ...[
                            'graph',
                            'panel',
                            'ordered-list',
                        ].map(page => `dashboard-widgets/${page}`),
                    ],
                },
                {
                    title: 'Generalities',
                    collapsable: false,
                    children: [
                        'building-menu',
                        'sharp-breadcrumb',
                        'entity-authorizations',
                        'how-to-transform-data',
                        'link-to',
                        'page-alerts',
                        'context',
                        'sharp-uploads',
                        'form-data-localization',
                        'testing-with-sharp',
                        'artisan-generators',
                        'style-visual-theme'
                    ]
                },
                {
                    title: 'Form fields',
                    collapsable: false,
                    children: [
                        'text',
                        'textarea',
                        'editor',
                        'number',
                        'html',
                        'check',
                        'date',
                        'upload',
                        'select',
                        'autocomplete',
                        'tags',
                        'list',
                        'autocomplete-list',
                        'geolocation',
                    ].map(page => `form-fields/${page}`),
                },
                {
                    title: 'Show fields',
                    collapsable: false,
                    children: [
                        'text',
                        'picture',
                        'list',
                        'file',
                        'embedded-entity-list',
                    ].map(page => `show-fields/${page}`),
                },
                {
                    title: 'Migrations guide',
                    collapsable: false,
                    children: [
                        'upgrading/7.0',
                        'upgrading/6.0',
                        'upgrading/5.0',
                        'upgrading/4.2',
                        'upgrading/4.1.3',
                        'upgrading/4.1',
                    ],
                },
            ]
        },
        algolia: {
            apiKey: 'd88cea985d718328d4b892ff6a05dba8',
            indexName: 'code16_sharp',
            // debug: true,
            algoliaOptions: {
                hitsPerPage: 5,
                facetFilters: [`tags:${DOCS_ALGOLIA_TAG}`],
            },
        }
    },
    markdown: {
        extendMarkdown: md => {
            md.renderer.rules['code_inline'] = (tokens, idx, options, env, slf) => {
                const token = tokens[idx];
                const highlighted = Prism.highlight(token.content, Prism.languages.php);
                const inlineNodes = tokens.filter(token =>
                    token.type === 'text' && token.content.trim()
                    || token.type === 'code_inline'
                );
                const isFullwidth = inlineNodes.length === 1;
                return `<code class="inline ${isFullwidth ? 'full' : ''}" v-pre>${highlighted}</code>`;
            };
        }
    },
    scss: {
        implementation: require('sass'),
    }
};
