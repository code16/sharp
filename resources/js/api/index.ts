import axios from 'axios';
import paramsSerializer from './paramsSerializer';
import { installInterceptors } from "./interceptors";
import { config } from "@/utils/config";

export const api = createApi();

export function createApi() {
    const api = axios.create({
        baseURL: `/${config('sharp.custom_url_segment')}/api`,
        paramsSerializer,
    });
    installInterceptors(api);
    return api;
}

export function getXsrfToken() {
    return document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/)?.[1];
}

export { handleErrorAlert, getErrorMessage } from './errors';
