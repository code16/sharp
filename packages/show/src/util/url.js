import { routeUrl } from 'sharp/router';

export function showUrl({ entityKey, instanceId }, options) {
    return routeUrl({
        name: 'show', params: { entityKey, instanceId },
    }, options);
}
