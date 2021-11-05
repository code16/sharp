import axios from 'axios';
import cookies from 'axios/lib/helpers/cookies';
import { API_PATH } from "../consts";
import paramsSerializer from './paramsSerializer';
import { installInterceptors } from "./interceptors";
import { getPreloadConfig } from "./preload";

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
    return cookies.read('XSRF-TOKEN');
}

export const preloadConfig = getPreloadConfig(api);
export { handleErrorAlert, getErrorMessage } from './errors';

