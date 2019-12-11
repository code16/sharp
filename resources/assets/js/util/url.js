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

function routeUrl(location) {
    const { href } = router().resolve(location);
    return href;
}

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item ? normalize(item.url) : null;
}

export function getListBackUrl(breadcrumb) {
    const listItem = breadcrumb.find(item => item.type === 'entityList');
    return normalize(listItem.url);
}

export function formUrl({ entityKey, instanceId }) {
    return normalize(routeUrl({
        name: 'form', params: { entityKey, instanceId },
    }));
}

export function listUrl(entityKey) {
    return normalize(routeUrl({
        name: 'entity-list', params: { id: entityKey },
    }));
}

export function showUrl({ entityKey, instanceId }) {
    return normalize(routeUrl({
        name: 'show', params: { entityKey, instanceId }
    }));
}