import { lang } from "../util/i18n";
import { showAlert } from "../util/dialogs";

export function getErrorMessage({ data, status }) {
    return lang(`modals.${status}.message`)
        || data.message
        || lang(`modals.error.message`);
}

export function handleErrorAlert({ data, method, status }) {
    const text = getErrorMessage({ data, status });
    const title = lang(`modals.${status}.title`) || lang(`modals.error.title`);

    if(status === 404 && method === 'get' || status === 422) {
        return;
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
}
