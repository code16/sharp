import { __ } from "@/utils/i18n";
import { showAlert } from "@/utils/dialogs";

export function getErrorMessage({ data, status }) {
    return __(`sharp::modals.${status}.message`) === `sharp::modals.${status}.message`
        ? `${status}: ${data.message}`
        : __(`sharp::modals.${status}.message`);
}

export async function handleErrorAlert({ data, method, status }) {
    if(status === 404 && method === 'get' || status === 422) {
        return;
    }

    // for 401 / 419 we directly redirect to the login page
    if(status === 401 || status === 419) {
        location.reload();
    } else {
        const text = getErrorMessage({ data, status });
        const title = __(`sharp::modals.api.${status}.title`) === `sharp::modals.api.${status}.title`
            ? __(`sharp::modals.error.title`)
            : __(`sharp::modals.api.${status}.title`);

        if(typeof data === 'string') {
            showAlert(data, { title, isError: true, isHtmlContent: true });
        } else {
            showAlert(text, { title, isError: true });
        }
    }
}
