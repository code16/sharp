import { store } from "../store/store";

let modalId = 0;

export function showDialog({ text, okCallback = ()=>{}, okCloseOnly, isError, ...props }) {
    const id = modalId++;
    function hiddenCallback() {
        store().dispatch('setDialogs', store().state.dialogs.filter(dialog => dialog.id !== id));
    }
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
            okCallback,
            hiddenCallback,
            text,
        }
    ]);
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
        ...props
    });
}