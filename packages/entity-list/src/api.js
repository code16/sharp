import { api } from 'sharp';


export function postEntityListReorder({ entityKey, instances }) {
    return api.post(`list/${entityKey}/reorder`, { instances });
}