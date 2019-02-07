import axios from 'axios';
import { API_PATH } from "./consts";

export const api = axios.create({ baseURL:API_PATH });

export function getDashboard({ dashboardKey, filters }) {
    return api.get(`dashboard/${dashboardKey}`, {
        params: {
            ...filters,
        },
    }).then(response => response.data);
}

export function postDashboardCommand({ dashboardKey, query, data }) {
    return api.post(`dashboard/${dashboardKey}/command`, {
        query,
        data,
    }, { responseType: 'blob' });
}

export function getDashboardCommandFormData({ dashboardKey, query }) {
    return api.get(`dashboard/${dashboardKey}/command/data`, {
        params: {
            ...query,
        },
    }).then(response => response.data.data);
}