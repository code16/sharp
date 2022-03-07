const mix = require('laravel-mix');
const webpack = require('webpack');
const path = require('path');
const CircularDependencyPlugin = require('circular-dependency-plugin');

mix.js('resources/assets/js/sharp.js', 'resources/assets/dist/sharp.js').vue()
    .js('resources/assets/js/client-api.js', 'resources/assets/dist/client-api.js')
    .sass('resources/assets/sass/app.scss', 'resources/assets/dist/sharp.css', { implementation:require('node-sass') })
    .sass('resources/assets/sass/vendors.scss', 'resources/assets/dist/vendors.css', { implementation:require('node-sass') })
    .copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'resources/assets/dist/fonts')
    .copy('node_modules/element-ui/lib/theme-chalk/fonts/*', 'resources/assets/dist/fonts')
    .copy('node_modules/leaflet/dist/images/*', 'resources/assets/dist/images')
    .options({
        processCssUrls: false,
        terser: {
            terserOptions: {
                format: {
                    comments: false,
                },
            },
            extractComments: false,
        },
    })
    .extract()
    .setResourceRoot('/vendor/sharp')
    .setPublicPath('resources/assets/dist')
    .webpackConfig({
        plugins: [
            new webpack.IgnorePlugin({
                resourceRegExp: /^\.\/locale$/,
                contextRegExp: /moment$/,
            }),
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
                },
                {
                    test: /bootstrap-vue\/esm\/(icons\/icons)/,
                    use: 'null-loader'
                }
            ]
        },
        resolve: {
            alias: {
                // resolve core-js@2.0 polyfills (now 3.0)
                'core-js/fn': 'core-js/features',
                'sharp/scss': path.resolve(__dirname, 'resources/assets/sass'),
            },
        }
    });


if(mix.inProduction()) {
    mix.version();
}
else {
    mix.webpackConfig({
        plugins: [
            new CircularDependencyPlugin({
                exclude: /node_modules/,
                failOnError: true,
                allowAsyncCycles: false,
                cwd: process.cwd(),
            }),
            // new (require('webpack-bundle-analyzer').BundleAnalyzerPlugin)
        ],
        resolve: {
            alias: {
                ...require('fs').existsSync('../packages/tiptap-markdown') && {
                    'tiptap-markdown': console.warn('\x1b[33m⚠️  Using local tiptap-markdown\n\n')
                        || path.resolve(__dirname, '../packages/tiptap-markdown')
                },
            },
            modules: [path.resolve(__dirname, 'node_modules'), 'node_modules'],
        }
    });
}

