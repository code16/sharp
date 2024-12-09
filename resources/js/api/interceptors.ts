import { parseBlobJSONContent } from "@/utils/request";
import { handleErrorAlert } from "./errors";
import { Axios, AxiosError, isCancel } from "axios";


export function installInterceptors(api: Axios) {
    api.interceptors.request.use(request => {
        request.headers['X-Current-Page-Url'] = location.href;
        return request;
    });

    api.interceptors.response.use(
        response => {
            if(!response.headers['content-type']?.includes('application/json')
                && !response.headers['content-disposition']?.includes('attachment')
            ) {
                throw new Error(
                    `${response.config.method.toUpperCase()} ${response.config.url} :` +
                    `Invalid response content-type "${response.headers['content-type']}", expected "application/json"`
                );
            }
            return response;

        },
        async error => {
            if(isCancel(error)) {
                return Promise.reject(error);
            }
            if(error instanceof AxiosError) {
                const response = error.response;

                if(response.data instanceof Blob) {
                    if(response.data.type === 'application/json') {
                        response.data = await parseBlobJSONContent(response.data);
                    } else if(response.data.type === 'text/html') {
                        response.data = await response.data.text();
                    }
                }

                handleErrorAlert({
                    data: response.data,
                    status: response.status,
                    method: error.config.method,
                });
            }

            return Promise.reject(error);
        }
    );
}
