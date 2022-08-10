const path = require('path')

module.exports = ({ siteId, domains, url }) => ({
    name: 'fathom',
    clientConfigFile: path.resolve(__dirname, './clientConfig.js'),
    define: {
        FATHOM_ID: siteId,
        FATHOM_DOMAINS: domains,
        FATHOM_URL: url || null,
    },
});
