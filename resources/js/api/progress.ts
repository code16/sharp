import { Axios } from "axios";
import NProgress from 'nprogress';

export const progressDelay = 150;

export function installProgressInterceptors(api: Axios) {
    let timeout = null;
    let pendingRequests = 0;

    function start() {
        pendingRequests++;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            NProgress.start();
        }, progressDelay);
    }

    function done() {
        pendingRequests--;
        clearTimeout(timeout);
        if(!pendingRequests) {
            NProgress.done();
        }
    }

    api.interceptors.request.use((config) => {
        start();
        return config;
    }, (error) => {
        done();
        return Promise.reject(error);
    });
    api.interceptors.response.use((response) => {
        done();
        return response;
    }, (error) => {
        done();
        return Promise.reject(error);
    });
}
