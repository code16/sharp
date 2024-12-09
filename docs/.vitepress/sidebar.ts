import type {DefaultTheme} from "vitepress";

export function sidebar(): DefaultTheme.SidebarItem[] {
    return [
        {
            text: 'Introduction',
            items: [
                { text: 'Getting started and installation', link: '/guide/index.md' },
                { text: 'The entity class', link: '/guide/entity-class.md' },
                { text: 'The Menu', link: '/guide/building-menu.md' }
            ]
        },
        {
            text: 'Entity Lists',
            collapsed: true,
            items: [
                { text: 'Create an Entity List', link: '/guide/building-entity-list.md' },
                { text: 'Filters', link: '/guide/filters.md' },
                { text: 'Entity States', link: '/guide/entity-states.md' },
                { text: 'Reordering instances', link: '/guide/reordering-instances.md' },
                { text: 'Avoid n+1 queries in Entity Lists', link: '/guide/avoid-n1-queries-in-entity-lists.md' }
            ]
        },
        {
            text: 'Forms',
            collapsed: true,
            items: [
                { text: 'Create a Form', link: '/guide/building-form.md' },
                { text: 'Multi-Forms', link: '/guide/multiforms.md' },
                { text: 'Using Single Form for unique resources', link: '/guide/single-form.md' },
                { text: 'Write an Embed for the Editor field', link: '/guide/form-editor-embeds.md' },
                { text: 'Text', link: '/guide/form-fields/text.md' },
                { text: 'Textarea', link: '/guide/form-fields/textarea.md' },
                { text: 'Editor', link: '/guide/form-fields/editor.md' },
                { text: 'Number', link: '/guide/form-fields/number.md' },
                { text: 'Html', link: '/guide/form-fields/html.md' },
                { text: 'Check', link: '/guide/form-fields/check.md' },
                { text: 'Date', link: '/guide/form-fields/date.md' },
                { text: 'Upload', link: '/guide/form-fields/upload.md' },
                { text: 'Select', link: '/guide/form-fields/select.md' },
                { text: 'Autocomplete', link: '/guide/form-fields/autocomplete.md' },
                { text: 'Tags', link: '/guide/form-fields/tags.md' },
                { text: 'List', link: '/guide/form-fields/list.md' },
                { text: 'AutocompleteList', link: '/guide/form-fields/autocomplete-list.md' },
                { text: 'Geolocation', link: '/guide/form-fields/geolocation.md' },
                { text: 'Custom form field', link: '/guide/custom-form-fields.md' }
            ]
        },
        {
            text: 'Show Pages',
            collapsed: true,
            items: [
                { text: 'Create a Show Page', link: '/guide/building-show-page.md' },
                { text: 'Using Single Show for unique resources', link: '/guide/single-show.md' },
                { text: 'Text', link: '/guide/show-fields/text.md' },
                { text: 'Picture', link: '/guide/show-fields/picture.md' },
                { text: 'List', link: '/guide/show-fields/list.md' },
                { text: 'File', link: '/guide/show-fields/file.md' },
                { text: 'Embedded EntityList', link: '/guide/show-fields/embedded-entity-list.md' },
                { text: 'Custom show field', link: '/guide/custom-show-fields.md' }
            ]
        },
        {
            text: 'Dashboards',
            collapsed: true,
            items: [
                { text: 'Create a Dashboard', link: '/guide/building-dashboard.md' },
                { text: 'Graph widget', link: '/guide/dashboard-widgets/graph.md' },
                { text: 'Panel widget', link: '/guide/dashboard-widgets/panel.md' },
                { text: 'Figure widget', link: '/guide/dashboard-widgets/figure.md' },
                { text: 'Ordered list widget', link: '/guide/dashboard-widgets/ordered-list.md' }
            ]
        },
        {
            text: 'Commands',
            collapsed: true,
            items: [
                { text: 'Write a Command', link: '/guide/commands.md' },
                { text: 'Write a Wizard Command', link: '/guide/commands-wizard.md' }
            ]
        },
        {
            text: 'Authentication and authorizations',
            collapsed: true,
            items: [
                { text: 'Authentication', link: '/guide/authentication.md' },
                { text: 'Entity authorizations', link: '/guide/entity-authorizations.md' }
            ]
        },
        {
            text: 'Generalities',
            collapsed: true,
            items: [
                { text: 'Sharp’s breadcrumb', link: '/guide/sharp-breadcrumb.md' },
                { text: 'Implement global search', link: '/guide/global-search.md' },
                { text: 'How to transform data', link: '/guide/how-to-transform-data.md' },
                { text: 'Create links to an entity', link: '/guide/link-to.md' },
                { text: 'Add global page alert', link: '/guide/page-alerts.md' },
                { text: 'Sharp Context', link: '/guide/context.md' },
                { text: 'Sharp built-in solution for uploads', link: '/guide/sharp-uploads.md' },
                { text: 'Data localization in Form and Show Page', link: '/guide/data-localization.md' },
                { text: 'Testing with Sharp', link: '/guide/testing-with-sharp.md' },
                { text: 'Artisan Generators', link: '/guide/artisan-generators.md' },
                { text: 'Style & Visual Theme', link: '/guide/style-visual-theme.md' }
            ]
        },
        {
            text: 'Migrations guide',
            collapsed: true,
            items: [
                { text: 'Upgrading from 8.x to 9.x', link: '/guide/upgrading/9.0.md' },
                { text: 'Upgrading from 7.x to 8.x', link: '/guide/upgrading/8.0.md' },
                { text: 'Upgrading from 6.x to 7.x', link: '/guide/upgrading/7.0.md' },
                { text: 'Upgrading from 5.x to 6.x', link: '/guide/upgrading/6.0.md' },
                { text: 'Upgrading from 4.2.x to 5.x', link: '/guide/upgrading/5.0.md' },
                { text: 'Upgrading from 4.1.x to 4.2', link: '/guide/upgrading/4.2.md' },
                { text: 'Upgrading from 4.1 to 4.1.3', link: '/guide/upgrading/4.1.3.md' },
                { text: 'Upgrading from 4.0 to 4.1', link: '/guide/upgrading/4.1.md' }
            ]
        }
    ];
}
