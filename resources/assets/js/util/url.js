import { router, stringifyQuery, parseQuery } from '../router';

const defaultQuery = {
    'x-access-from': 'ui'
};

function normalize(url) {
    const urlLocation = /^\//.test(url)
        ? new URL(url, location.origin)
        : new URL(url);
    const query = {
        ...parseQuery(urlLocation.search),
        ...defaultQuery,
    };
    return urlLocation.pathname + stringifyQuery(query);
}

export function routeUrl(location, { normalized=true }={}) {
    const { href } = router().resolve(location);
    return normalized ? normalize(href) : href;
}

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? normalize(item.url) : null;
}

export function getListBackUrl(breadcrumb) {
    const listItem = breadcrumb.find(item => item.type === 'entityList');
    return normalize(listItem.url);
}

export function getBaseUrl() {
    const meta = document.head.querySelector('meta[name=base-url]');
    return meta ? `/${meta.content}` : '/sharp';
}