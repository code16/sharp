import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';
import { API_PATH } from "./consts";
import paramsSerializer from './helpers/paramsSerializer';

export const api = axios.create({
    baseURL: API_PATH,
    paramsSerializer,
});

export function apiUrl(url, { params } = {}) {
    return api.getUri({
        url: `${API_PATH}/${url.replace(/^\//, '')}`,
        params,
    });
}

export function getXsrfToken() {
    return cookies.read('XSRF-TOKEN');
}