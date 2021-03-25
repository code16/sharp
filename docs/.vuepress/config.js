var Prism = require('prismjs');
var loadLanguages = require('prismjs/components/');
loadLanguages(['php']);

module.exports = {
    title: 'Sharp',
    base: '/docs/',
    themeConfig: {
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Documentation', link: '/guide/' },
            { text: 'Demo', link: 'http://sharp.code16.fr/sharp/' },
            { text: 'Github', link:'https://github.com/code16/sharp' },
            { text: 'Medium', link:'https://medium.com/code16/tagged/sharp' },
        ],
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
                    title: 'Entity Forms',
                    collapsable: false,
                    children: [
                        'building-entity-form',
                        'multiforms',
                        'single-form',
                        'custom-form-fields'
                    ]
                },
                {
                    title: 'Entity Shows',
                    collapsable: false,
                    children: [
                        'building-entity-show',
                        'single-show',
                        'custom-show-fields'
                    ]
                },
                {
                    title: 'Dashboards',
                    collapsable: false,
                    children: [
                        'dashboard',
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
                        'context',
                        'sharp-built-in-solution-for-uploads',
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
                        'markdown',
                        'wysiwyg',
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
            },
        }
    },
    markdown: {
        extendMarkdown: md => {
            md.renderer.rules['code_inline'] = (tokens, idx, options, env, slf) => {
                let token = tokens[idx];
                return '<code class="inline">' +
                    Prism.highlight(token.content, Prism.languages.php) +
                '</code>';
            };
        }
    },
    scss: {
        implementation: require('sass'),
    }
};