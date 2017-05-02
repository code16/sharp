const { mix } = require('laravel-mix');



mix.autoload({})
    .js('resources/assets/js/sharp.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css/sharp.css')
    .copy('node_modules/vue-multiselect/dist/vue-multiselect.min.css', 'public/css/vue-multiselect.min.css')
    .webpackConfig({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        }
    });