module.exports = {
    title: 'Sharp',
    themeConfig: {
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Documentation', link: '/pages/' },
            { text: 'Github', link:'https://github.com/code16/sharp' },
            { text: 'Medium', link:'https://medium.com/code16/tagged/sharp' },
        ],
        sidebar: {
            '/pages/': [
                '',
                'authentication',
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
                        'entity-authorizations',
                        'multiforms',
                        'custom-form-fields'
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
                    title: 'Dashboard',
                    collapsable: false,
                    children: [
                        'dashboard',
                        ...[
                            'graph',
                            'panel',
                        ].map(page => `dashboard-widgets/${page}`),
                    ],
                },
                {
                    title: 'Generalities',
                    collapsable: false,
                    children: [
                        'building-menu',
                        'how-to-transform-data',
                        'context',
                        'sharp-built-in-solution-for-uploads',
                        'form-data-localization',
                        'testing-with-sharp',
                        'artisan-generators'
                    ]
                },
                'style-visual-theme'
            ],
        },
    }
};