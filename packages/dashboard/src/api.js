import { api } from 'sharp';

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