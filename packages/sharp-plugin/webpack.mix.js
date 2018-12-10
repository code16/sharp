let mix = require('laravel-mix');

mix.js('src/index.js', 'dist/sharp-plugin.js')
    .webpackConfig({
        output: {
            libraryTarget:'umd'
        }
    });