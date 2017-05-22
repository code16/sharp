const { mix } = require('laravel-mix');



mix.autoload({})
    .copy('node_modules/vue-clip/src', 'resources/assets/js/components/fields/upload/vue-clip')
    .js('resources/assets/js/sharp.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css/sharp.css')
    .styles([
        'node_modules/vue-multiselect/dist/vue-multiselect.min.css',
        'node_modules/simplemde/dist/simplemde.min.css'
    ],'public/css/vendor.css')
    /*.options({
        extractVueStyles:true
    });*/
    /*.webpackConfig({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        }
    });*/