import { parseBlobJSONContent } from "../util/request";
import { lang } from "../util/i18n";
import { showAlert } from "../util/dialogs";


export function installInterceptors(api) {
    api.interceptors.response.use(r => r, async error => {
        const response = error.response;
        const method = error.config.method;

        if(response.data instanceof Blob && response.data.type === 'application/json') {
            response.data = await parseBlobJSONContent(response.data);
        }

        const { data, status } = response;

        const text = data.message || lang(`modals.${status}.message`) || lang(`modals.error.message`);
        const title = lang(`modals.${status}.title`) || lang(`modals.error.title`);

        if(status === 404 && method === 'get' || status === 422) {
            return Promise.reject(error);
        }

        if(status === 401 || status === 419) {
            showAlert(text, {
                title,
                isError: true,
                okCallback() {
                    location.reload();
                },
            });
        }
        else {
            showAlert(text, { title, isError:true });
        }

        return Promise.reject(error);
    });
}
