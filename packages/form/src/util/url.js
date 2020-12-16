import { routeUrl, router } from 'sharp/router';

export function formUrl({ entityKey, instanceId }, options) {
    return routeUrl({
        name: 'form', params: { entityKey, instanceId },
    }, options);
}
