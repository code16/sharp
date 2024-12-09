/**
 * @type {import('@babel/core').TransformOptions}
 */
module.exports = {
    presets: [
        [
            '@babel/preset-env',
            {
                "useBuiltIns": "usage",
                "corejs": "3.36"
            }
        ],
    ],
    env: {
        'test': {
            presets: [
                ['@babel/preset-env', {
                    targets: {
                        node: true
                    },
                }]
            ],
        }
    }
};
