const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');

mix.js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js')
    .js('resources/assets/js/client-api.js', 'resources/assets/dist/client-api.js')
    .sass('resources/assets/sass/app.scss', 'resources/assets/dist/sharp.css')
    .sass('resources/assets/sass/cms.scss', 'resources/assets/dist/sharp-cms.css')
    .copy('node_modules/font-awesome/fonts','resources/assets/dist/fonts')
    .options({
        processCssUrls: false
    })
    .version()
    .extract()
    .setPublicPath('resources/assets/dist')
    .webpackConfig({
        plugins: [
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
            // new (require('webpack-bundle-analyzer').BundleAnalyzerPlugin)
        ],
        // transpile vue-clip package
        module: {
            rules: [
                {
                    test: /\.js$/,
                    include: [
                        path.resolve(__dirname, 'node_modules/vue-clip')
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
                'core-js/fn': 'core-js/features'
            }
        }
    });



