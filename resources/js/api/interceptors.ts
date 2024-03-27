import { parseBlobJSONContent } from "@/utils/request";
import { handleErrorAlert } from "./errors";


export function installInterceptors(api) {
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
        }
    );
}
