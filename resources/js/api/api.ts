import axios from 'axios';
import paramsSerializer from './paramsSerializer';
import { installInterceptors } from "./interceptors";
import { config } from "@/utils/config";
import { installProgressInterceptors } from "@/api/progress";

export const api = createApi();

export function createApi() {
    const api = axios.create({
        baseURL: `/${config('sharp.custom_url_segment')}/api`,
        paramsSerializer,
    });
    installInterceptors(api);
    installProgressInterceptors(api);
    return api;
}

export { handleErrorAlert, getErrorMessage } from './errors';
