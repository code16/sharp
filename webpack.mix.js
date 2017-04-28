const { mix } = require('laravel-mix');



mix.autoload({})
    .js('resources/assets/js/sharp.js', 'public/js')
    .styles(['resources/assets/theme/sharp/index.css'],'public/css/theme.css')
    .sass('resources/assets/sass/app.scss', 'public/css/sharp.css')
    .copy('node_modules/bootstrap/dist/css/bootstrap-grid.css', 'public/css/bootstrap-grid.css')
    .webpackConfig({
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm.js'
            }
        }
    });