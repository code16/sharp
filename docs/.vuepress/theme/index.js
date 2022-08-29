import { defaultTheme } from '@vuepress/theme-default';
import { path } from '@vuepress/utils';

/**
 * @param {import('@vuepress/theme-default').DefaultThemeOptions} options
 * @return {import('@vuepress/core').ThemeObject}
 */
export default (options) => ({
    name: 'vuepress-theme-local',
    extends: defaultTheme(options),
    clientConfigFile: path.resolve(__dirname, './client.js'),
    alias: {
        '@theme/NavbarBrand.vue': path.resolve(__dirname, './components/NavbarBrand.vue'),
    },
});
