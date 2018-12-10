import axios from 'axios';
import { API_PATH } from "./consts";

export const api = axios.create({ baseURL:API_PATH });

export function getDashboard({ dashboardKey, filters }) {
    return api.get(`dashboard/${dashboardKey}`, {
        params: { ...filters }
    }).then(response => response.data);
}