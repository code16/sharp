import { routeUrl } from 'sharp/router';

export function listUrl(entityKey) {
    return routeUrl({
        name: 'entity-list', params: { entityKey },
    });
}