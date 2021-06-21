import { parseBlobJSONContent } from "../util/request";
import { handleErrorAlert } from "./errors";


export function installInterceptors(api) {
    api.interceptors.response.use(r => r, async error => {
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
