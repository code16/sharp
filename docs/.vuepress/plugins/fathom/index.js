import * as path from 'path';

export default ({ siteId, domains, url }) => ({
    name: 'fathom',
    clientConfigFile: path.resolve(__dirname, './clientConfig.js'),
    define: {
        FATHOM_ID: siteId,
        FATHOM_DOMAINS: domains,
        FATHOM_URL: url || null,
    },
});
