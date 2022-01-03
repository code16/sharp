/// <reference types="cypress" />
// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)


// const webpack = require('@cypress/webpack-preprocessor');

// const dotenvPlugin = require('cypress-dotenv');

module.exports = (on, config) => {
  // config = dotenvPlugin(config, {
  //   path: '../.env',
  // }, true);

  if (config.testingType === 'component') {
    const { startDevServer } = require('@cypress/webpack-dev-server')
    const webpackConfig = require('@vue/cli-service/webpack.config.js');
    const path = require('path');

    on('dev-server:start', (options) =>
        startDevServer({
          options,
          webpackConfig,
          template: path.resolve('public/index.html'),
        })
    )
  }

  // return {
  //   ...config,
  //   // baseUrl: config.env.APP_URL,
  // }
};
