const { mix } = require('laravel-mix');
const webpack = require('webpack');
const fs = require('fs-extra');

(function() {
    mix.copy('node_modules/vue-clip/src', 'resources/assets/js/components/vendor/vue-clip')
        .copy('node_modules/bootstrap-vue/lib', 'resources/assets/js/components/vendor/bootstrap-vue')
        .copy('node_modules/vue2-timepicker/src', 'resources/assets/js/components/vendor/vue2-timepicker');

    if(process.env.NODE_ENV === 'test') {
        mix.then(function() {
            fs.removeSync('mix-manifest.json');
        });
        return;
    }

    mix.autoload({}).options({
            extractVueStyles:'.bin/vendor-components.css',
            processCssUrls: false
        })
        .setPublicPath('resources/assets/dist')

        .js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
        //.js('resources/assets/js/sharp-embedded.js', 'resources/assets/dist/sharp-embedded.js')

        .copy('node_modules/font-awesome/fonts','resources/assets/dist/fonts')
        //.copy('node_modules/carbon-icons/dist/carbon-icons.svg','resources/assets/dist/fonts/carbon-icons.svg')
        .sass('resources/assets/sass/app.scss', 'resources/assets/dist/.bin/sharp-app.css')
        .sass('resources/assets/sass/vendors.scss', 'resources/assets/dist/.bin/vendors.css')
        .sass('resources/assets/sass/cms.scss', 'resources/assets/dist/sharp-cms.css')
        .styles([
            'resources/assets/dist/.bin/vendors.css',
            'resources/assets/dist/.bin/sharp-app.css',
            //'node_modules/vue-multiselect/dist/vue-multiselect.min.css',
            'node_modules/simplemde/dist/simplemde.min.css',
        ], 'resources/assets/dist/sharp.css')
        .webpackConfig({
            plugins: [
                new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
                new webpack.WatchIgnorePlugin([/\/vendor\//]) //Do not watch files which are inside a 'vendor/' directories
            ]
        })
        .then(function() {
            fs.removeSync('resources/assets/dist/.bin');
        });
})();



