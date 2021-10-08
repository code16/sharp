import { api, apiUrl } from 'sharp';

export function getShowView({ entityKey, instanceId }) {
    return api.get(`show/${entityKey}/${instanceId || ''}`)
        .then(response => response.data);
}

export function postShowCommand({ entityKey, instanceId, commandKey, data }) {
    return api.post(`show/${entityKey}/command/${commandKey}/${instanceId || ''}`, {
        data,
    }, { responseType: 'blob' });
}

export function getShowCommandFormData({ entityKey, instanceId, commandKey }) {
    return api.get(`show/${entityKey}/command/${commandKey}${instanceId ? `/${instanceId}` : ''}/data`)
        .then(response => response.data.data);
}

export function postShowState({ entityKey, instanceId, value }) {
    return api.post(`show/${entityKey}/state/${instanceId || ''}`, { value })
        .then(response => response.data);
}

export function downloadFileUrl({ entityKey, instanceId, fieldKey, fileName }) {
    return apiUrl(`show/download/${fieldKey}/${entityKey}/${instanceId}`, {
        params: {
            fileName,
        },
    })
}
