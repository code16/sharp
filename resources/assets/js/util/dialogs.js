import { store } from "../store/store";
import { lang } from "./i18n";

let modalId = 0;

export function showDialog({ text, okCallback = ()=>{}, okCloseOnly, isError, ...props }) {
    const id = modalId++;

    function hiddenCallback() {
        store().dispatch('setDialogs', store().state.dialogs.filter(dialog => dialog.id !== id));
    }

    return new Promise((resolve) => {
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
                okCallback: resolve,
                hiddenCallback,
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
        okTitle: lang('modals.confirm.ok_button'),
        bodyClass: 'pt-4',
        ...props
    });
}
