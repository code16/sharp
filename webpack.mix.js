const { mix } = require('laravel-mix');



mix.autoload({})
    .js('resources/assets/js/sharp.js', 'public/js')
    .styles(['resources/assets/theme/sharp/index.css'],'public/css/theme.css')
    .webpackConfig({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        }
    });