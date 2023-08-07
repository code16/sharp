import { store } from "../store/store";
import { __, lang } from "./i18n";

let modalId = 0;

export function showDialog({ text, okCallback = ()=>{}, okCloseOnly, isError, ...props }) {
    const id = modalId++;

    return new Promise((resolve, reject) => {
        store().dispatch('setDialogs', [
            ...store().state.dialogs,
            {
                id,
                props: {
                    ...props,
                    okOnly: okCloseOnly,
                    noCloseOnBackdrop: okCloseOnly,
                    noCloseOnEsc: okCloseOnly,
                    visible: true,
                    isError
                },
                okCallback: () => resolve(true),
                hiddenCallback: () => {
                    store().dispatch('setDialogs', store().state.dialogs.filter(dialog => dialog.id !== id));
                    resolve(false);
                },
                text,
            }
        ]);
    });
}

export function showAlert(message, { title, ...props } = {}) {
    return showDialog({
        okCloseOnly: true,
        text: message,
        title,
        ...props
    });
}

export function showConfirm(message, { title, ...props } = {}) {
    return showDialog({
        text: message,
        title,
        size: 'sm',
        hideHeader: true,
        okTitle: __('sharp::modals.confirm.ok_button'),
        bodyClass: 'pt-4',
        ...props
    });
}

export function showDeleteConfirm(message) {
    return showConfirm(message, {
        okTitle: __('sharp::modals.confirm.delete.ok_button'),
        okVariant: 'danger',
    });
}
