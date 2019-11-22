import { router } from '../router';

function routeUrl(location) {
    const { href } = router().resolve(location);
    return href;
}

export function getBackUrl(breadcrumb) {
    const item = breadcrumb[breadcrumb.length - 2];
    return item.url;
}

export function getListBackUrl(breadcrumb) {
    const listItem = breadcrumb.find(item => item.type === 'entityList');
    return listItem.url;
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