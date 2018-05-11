const { mix } = require('laravel-mix');
const webpack = require('webpack');
const fs = require('fs-extra');

(function() {
    fs.copySync('node_modules/vue-clip/src', 'resources/assets/js/components/vendor/vue-clip');
    fs.copySync('node_modules/vue2-timepicker/src', 'resources/assets/js/components/vendor/vue2-timepicker');
    fs.copySync('node_modules/vue2-google-maps/src', 'resources/assets/js/components/vendor/vue2-google-maps');

    if(process.env.NODE_ENV === 'test') {
        mix.then(function() {
            fs.removeSync('mix-manifest.json');
        });
        return;
    }

    mix
        .copy('node_modules/font-awesome/fonts','resources/assets/dist/fonts')
        .js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
        .js('resources/assets/js/api.js', 'resources/assets/dist/api.js')
        .sass('resources/assets/sass/app.scss', 'resources/assets/dist/.bin/sharp-app.css')
        .sass('resources/assets/sass/vendors.scss', 'resources/assets/dist/.bin/vendors.css')
        .sass('resources/assets/sass/cms.scss', 'resources/assets/dist/sharp-cms.css')
        .styles([
            'resources/assets/dist/.bin/vendors.css',
            'resources/assets/dist/.bin/sharp-app.css',
            'node_modules/simplemde/dist/simplemde.min.css',
            'node_modules/trix/dist/trix.css'
        ], 'resources/assets/dist/sharp.css')
        .options({
            extractVueStyles:'.bin/vendor-components.css',
            processCssUrls: false
        })
        .version() // hash modules id to prevent useless js files changed
        .extract(['vue'])
        .setPublicPath('resources/assets/dist')
        .webpackConfig({
            plugins: [
                new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
                new webpack.WatchIgnorePlugin([/\/vendor\//]) //Do not watch files which are inside a 'vendor/' directories
            ],
            resolve: {
                alias: {
                    'vue$': 'vue/dist/vue.common.js'
                }
            }
        })
        .then(function() {
            fs.removeSync('resources/assets/dist/.bin');
        });
})();



