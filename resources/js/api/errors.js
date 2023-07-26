import { lang } from "../util/i18n";
import { showAlert } from "../util/dialogs";

export function getErrorMessage({ data, status }) {
    return lang(`modals.${status}.message`, null)
        || `${status}: ${data.message}`
        || lang(`modals.error.message`);
}

export async function handleErrorAlert({ data, method, status }) {
    const text = getErrorMessage({ data, status });
    const title = lang(`modals.${status}.title`, null) || lang(`modals.error.title`);

    if(status === 404 && method === 'get' || status === 422) {
        return;
    }

    await showAlert(text, { title, isError: true });

    if(status === 401 || status === 419) {
        location.reload();
    }
}
