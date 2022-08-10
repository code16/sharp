const { defaultTheme } = require('@vuepress/theme-default');
const { path } = require('@vuepress/utils');

/**
 * @param {import('@vuepress/theme-default').DefaultThemeOptions} options
 */
module.exports = (options) => ({
    name: 'vuepress-theme-local',
    extends: defaultTheme(options),
    layouts: path.resolve(__dirname, './layouts'),
    alias: {
        '@theme/NavbarBrand.vue': path.resolve(__dirname, './components/NavbarBrand.vue'),
    },
});
