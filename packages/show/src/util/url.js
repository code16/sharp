import { routeUrl } from 'sharp/router';

export function showUrl({ entityKey, instanceId }) {
    return routeUrl({
        name: 'show', params: { entityKey, instanceId }
    });
}