import { routeUrl } from 'sharp/router';

export function formUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'form', params: { entityKey, instanceId },
    });
}