import { parseBlobJSONContent } from "../util/request";
import { handleErrorAlert } from "./errors";


export function installInterceptors(api) {
    api.interceptors.response.use(response => {
        handleInvalidContentType(response);
        return response;

    }, async error => {
        const response = error.response;

        if(response.data instanceof Blob && response.data.type === 'application/json') {
            response.data = await parseBlobJSONContent(response.data);
        }

        handleErrorAlert({
            data: response.data,
            status: response.status,
            method: error.config.method,
        });

        return Promise.reject(error);
    });
}


function handleInvalidContentType(response) {
    const contentType = response.headers['content-type'];
    if(!contentType?.includes('application/json')
        && !response.headers['content-disposition']?.includes('attachment')
    ) {
        const { method, url } = response.config;
        const message = `${method.toUpperCase()} ${url} : Invalid response content-type "${contentType}"`;
        console.error(message);
        throw new Error(message);
    }
}
