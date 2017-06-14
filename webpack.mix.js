const { mix } = require('laravel-mix');
const webpack = require('webpack');
const fs = require('fs-extra');

mix.autoload({})
    .setPublicPath('resources/assets/dist')

    .copy('node_modules/vue-clip/src', 'resources/assets/js/components/vendor/vue-clip')
    .copy('node_modules/bootstrap-vue/lib', 'resources/assets/js/components/vendor/bootstrap-vue')
    .js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
    //.js('resources/assets/js/sharp-embedded.js', 'resources/assets/dist/sharp-embedded.js')

    .copy('node_modules/font-awesome/fonts','resources/assets/dist/fonts')
    .sass('resources/assets/sass/app.scss', 'resources/assets/dist/.bin/sharp-app.css')
    .styles([
        'resources/assets/dist/.bin/sharp-app.css',
        'node_modules/vue-multiselect/dist/vue-multiselect.min.css',
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