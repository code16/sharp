import { stringifyQuery, parseQuery } from './querystring';

const defaultQuery = {
    'x-access-from': 'ui'
};

export function normalizeUrl(url) {
    const urlLocation = /^\//.test(url)
        ? new URL(url, location.origin)
        : new URL(url);
    const query = {
        ...parseQuery(urlLocation.search),
        ...defaultQuery,
    };
    return urlLocation.pathname + stringifyQuery(query);
}

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? normalizeUrl(item.url) : null;
}

export function getListBackUrl(breadcrumb) {
    const listItem = breadcrumb.find(item => item.type === 'entityList');
    return normalizeUrl(listItem.url);
}