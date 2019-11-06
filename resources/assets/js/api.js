import axios from 'axios';
import { API_PATH } from "./consts";
import paramsSerializer from './helpers/paramsSerializer';

export const api = axios.create({
    baseURL: API_PATH,
    paramsSerializer,
});

export function getDashboard({ dashboardKey, filters }) {
    return api.get(`dashboard/${dashboardKey}`, {
        params: {
            ...filters,
        },
    }).then(response => response.data);
}

export function postDashboardCommand({ dashboardKey, commandKey, query, data }) {
    return api.post(`dashboard/${dashboardKey}/command/${commandKey}`, {
        query,
        data,
    }, { responseType: 'blob' });
}

export function getDashboardCommandFormData({ dashboardKey, commandKey, query }) {
    return api.get(`dashboard/${dashboardKey}/command/${commandKey}/data`, {
        params: {
            ...query,
        },
    }).then(response => response.data.data);
}

export function postEntityListReorder({ entityKey, instances }) {
    return api.post(`list/${entityKey}/reorder`, { instances });
}

export function getShowView({ entityKey, instanceId }) {
    return api.get(`show/${entityKey}/${instanceId || ''}`)
        .then(response => response.data);
}

export function getGlobalFilters() {
    return api.get(`filters`).then(response => response.data);
}

export function postGlobalFilters({ filterKey, value }) {
    return api.post(`filters/${filterKey}`, { value });
}

export function getAutocompleteSuggestions({ url, method, locale, searchAttribute, query, }) {
    const params = {
        locale,
        [searchAttribute]: query,
    };
    if(method.toLowerCase() === 'get') {
        return axios.get(url, { params})
            .then(response => response.data);
    } else {
        return axios.post(url, params)
            .then(response => response.data);
    }
}