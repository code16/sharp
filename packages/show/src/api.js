import { api, apiUrl } from 'sharp';

export function getShowView({ entityKey, instanceId }) {
    return api.get(`show/${entityKey}/${instanceId || ''}`)
        .then(response => response.data);
}

export function deleteShow({ entityKey, instanceId }) {
    return api.delete(`show/${entityKey}/${instanceId || ''}`)
        .then(response => response.data);
}

export function postShowCommand({ entityKey, instanceId, commandKey, data }) {
    return api.post(`show/${entityKey}/command/${commandKey}/${instanceId || ''}`, {
        ...data,
    }, { responseType: 'blob' });
}

export function getShowCommandForm({ entityKey, instanceId, commandKey, query }) {
    return api.get(`show/${entityKey}/command/${commandKey}${instanceId ? `/${instanceId}` : ''}/form`, {
        params: {
            ...query,
        },
    }).then(response => response.data);
}

export function postShowState({ entityKey, instanceId, value }) {
    return api.post(`show/${entityKey}/state/${instanceId || ''}`, { value })
        .then(response => response.data);
}

