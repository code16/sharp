import { Axios } from "axios";
import { progress } from '@inertiajs/core';

export const progressDelay = 150;

export function installProgressInterceptors(api: Axios) {
    let timeout = null;
    let pendingRequests = 0;

    function start() {
        pendingRequests++;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            progress.reveal();
            progress.start();
        }, progressDelay);
    }

    function done() {
        pendingRequests--;
        clearTimeout(timeout);
        if(!pendingRequests) {
            progress.finish();
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
