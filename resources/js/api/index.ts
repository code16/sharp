import axios from 'axios';
import { API_PATH } from "../consts";
import paramsSerializer from './paramsSerializer';
import { installInterceptors } from "./interceptors";

export const api = createApi();

export function createApi() {
    const api = axios.create({
        baseURL: API_PATH,
        paramsSerializer,
    });
    installInterceptors(api);
    return api;
}

export function apiUrl(url, { params } = {}) {
    return api.getUri({
        url: `${API_PATH}/${url.replace(/^\//, '')}`,
        params,
    });
}

export function getXsrfToken() {
    return document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)?.[1];
}

export { handleErrorAlert, getErrorMessage } from './errors';
