const { mix } = require('laravel-mix');
const webpack = require('webpack');

//console.log("'"+process.env.NODE_ENV+"'");
//const isProduction = process.env.NODE_ENV === 'production';


mix.autoload({})
    .webpackConfig({
        plugins: [
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
        ],
    })
    .copy('node_modules/vue-clip/src', 'resources/assets/js/components/vendor/vue-clip')
    .copy('node_modules/bootstrap-vue/lib', 'resources/assets/js/components/vendor/bootstrap-vue')
    .js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
    .sass('resources/assets/sass/app.scss', 'resources/assets/dist/sharp-only.css')
    .styles([
        'resources/assets/dist/sharp-only.css',
        'node_modules/vue-multiselect/dist/vue-multiselect.min.css',
        'node_modules/simplemde/dist/simplemde.min.css'
    ],'resources/assets/dist/sharp.css');
    /*.options({
        extractVueStyles:true
    });*/