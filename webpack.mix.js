const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');
const CircularDependencyPlugin = require('circular-dependency-plugin');

mix.js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
    .js('resources/assets/js/client-api.js', 'resources/assets/dist/client-api.js')
    .sass('resources/assets/sass/app.scss', 'resources/assets/dist/sharp.css')
    .sass('resources/assets/sass/cms.scss', 'resources/assets/dist/sharp-cms.css')
    .copy('node_modules/element-ui/packages/theme-chalk/lib/fonts', 'resources/assets/dist/fonts')
    .copy('node_modules/font-awesome/fonts', 'resources/assets/dist/fonts')
    .copy('node_modules/leaflet/dist/images','resources/assets/dist/images')
    .options({
        processCssUrls: false
    })
    .version()
    .extract()
    .setPublicPath('resources/assets/dist')
    .webpackConfig({
        plugins: [
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
            new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/en'),
            // new (require('webpack-bundle-analyzer').BundleAnalyzerPlugin)
            new CircularDependencyPlugin({
                // exclude detection of files based on a RegExp
                exclude: /node_modules/,
                // add errors to webpack instead of warnings
                failOnError: true,
                // allow import cycles that include an asyncronous import,
                // e.g. via import(/* webpackMode: "weak" */ './file.js')
                allowAsyncCycles: false,
                // set the current working directory for displaying module paths
                cwd: process.cwd(),
              })
        ],
        // transpile vue-clip package
        module: {
            rules: [
                {
                    test: /\.js$/,
                    include: [
                        path.resolve(__dirname, 'node_modules/vue-clip'),
                        path.resolve(__dirname, 'node_modules/vue2-timepicker')
                    ],
                    use: [
                        {
                            loader: 'babel-loader',
                            options: Config.babel()
                        }
                    ]
                }
            ]
        },
        resolve: {
            alias: {
                // resolve core-js@2.0 polyfills (now 3.0)
                'core-js/fn': 'core-js/features',
                'sharp/scss': path.resolve(__dirname, 'resources/assets/sass'),
                'sharp': path.resolve(__dirname, 'resources/assets/js'),
            }
        }
    });



