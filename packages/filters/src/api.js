import { api } from 'sharp';

export function getGlobalFilters() {
    return api.get(`filters`).then(response => response.data);
}

export function postGlobalFilters({ filterKey, value }) {
    return api.post(`filters/${filterKey}`, { value });
}