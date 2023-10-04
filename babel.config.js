/**
 * @type {import('@babel/core').TransformOptions}
 */
module.exports = {
    presets: [
        [
            '@babel/preset-env',
            {
                "useBuiltIns": "usage",
                "corejs": "3.33"
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
