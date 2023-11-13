import { api } from 'sharp';


export function deleteEntityListInstance({ entityKey, instanceId }) {
    return api.delete(`list/${entityKey}/${instanceId ?? ''}`)
        .then(response => response.data);
}

export function postEntityListReorder({ entityKey, instances }) {
    return api.post(`list/${entityKey}/reorder`, { instances });
}
