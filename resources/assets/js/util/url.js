import { stringifyQuery, parseQuery } from './querystring';

const defaultQuery = {
    'x-access-from': 'ui'
};

export function normalizeUrl(url) {
    const urlLocation = new URL(url, location.origin);
    const query = {
        ...parseQuery(urlLocation.search),
        ...defaultQuery,
    };
    return urlLocation.pathname + stringifyQuery(query);
}