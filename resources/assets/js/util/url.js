import { router } from '../router';

function routeUrl(location) {
    const { href } = router().resolve(location);
    return href;
}

export function getBackUrl(breadcrumb) {
    return breadcrumb[breadcrumb.length - 2];
}

export function getListBackUrl(breadcrumb, entityKey) {
    // TODO retrieve this in breadcrumb
    return listUrl(entityKey);
}

export function formUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'form', params: { entityKey, instanceId },
    });
}

export function listUrl(entityKey) {
    return routeUrl({
        name: 'entity-list', params: { id: entityKey },
    });
}

export function showUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'show', params: { entityKey, instanceId }
    })
}