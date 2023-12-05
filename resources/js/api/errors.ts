import { __ } from "@/utils/i18n";
import { showAlert } from "@/utils/dialogs";

export function getErrorMessage({ data, status }) {
    return __(`sharp::modals.${status}.message`) === `sharp::modals.${status}.message`
        ? `${status}: ${data.message}`
        : __(`sharp::modals.${status}.message`);
}

export async function handleErrorAlert({ data, method, status }) {
    const text = getErrorMessage({ data, status });
    const title = __(`sharp::modals.${status}.title`) === `sharp::modals.${status}.title`
        ? __(`sharp::modals.error.title`)
        : __(`sharp::modals.${status}.title`);

    if(status === 404 && method === 'get' || status === 422) {
        return;
    }

    await showAlert(text, { title, isError: true });

    if(status === 401 || status === 419) {
        location.reload();
    }
}
