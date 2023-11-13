

/** @type {import('@jest/types').Config.InitialOptions} */
const config = {
    moduleFileExtensions: [
        "js",
        "vue"
    ],
    transform: {
        "^.+\\.js$": "babel-jest",
        "^.+\\.vue$": "@vue/vue2-jest"
    },
    snapshotSerializers: [
        "<rootDir>/packages/test-utils/src/htmlSnapshotBeautifier.js"
    ],
    moduleNameMapper: {
        "^vue$": "vue/dist/vue.common.js",
        "^sharp((/(.*))|$)": "<rootDir>/resources/assets/js$1",
        "\\.(css|less)$": "<rootDir>/packages/test-utils/src/setup/fileMock.js",
        '\\.(jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2|mp4|webm|wav|mp3|m4a|aac|oga)$': "<rootDir>/packages/test-utils/src/setup/fileMock.js",
    },
    transformIgnorePatterns: [
        "node_modules/(?!(vue2-google-maps|bootstrap-vue|vue-clip|vue2-timepicker))"
    ],
    testMatch: [
        "**/__tests__/**/?(*.)(spec|test).js?(x)"
    ],
    testEnvironment: "jsdom",
    testPathIgnorePatterns: [
        "/node_modules/",
        "/legacy/"
    ],
    verbose: true,
    setupFiles: [
        "<rootDir>/packages/test-utils/src/setup/mockument.js",
        "<rootDir>/packages/test-utils/src/setup/mockBootstrapVue.js",
        "<rootDir>/packages/test-utils/src/setup/setupVue.js",
        "<rootDir>/resources/assets/js/polyfill.js"
    ]
};

module.exports = config;
