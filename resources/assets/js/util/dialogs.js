import { store } from "../store/store";
import { lang } from "./i18n";
import once from 'lodash/once';

let modalId = 0;
const preventUnhandledDialogRejection = once(() => {
    window.addEventListener('unhandledrejection', (event) => {
        if(event.reason === 'Dialog cancelled') {
            console.log('Dialog cancelled');
            event.preventDefault();
        }
    });
});

export function showDialog({ text, okCallback = ()=>{}, okCloseOnly, isError, ...props }) {
    const id = modalId++;

    preventUnhandledDialogRejection();

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
                okCallback: resolve,
                hiddenCallback: () => {
                    store().dispatch('setDialogs', store().state.dialogs.filter(dialog => dialog.id !== id));
                    reject('Dialog cancelled');
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
        okTitle: lang('modals.confirm.ok_button'),
        bodyClass: 'pt-4',
        ...props
    });
}

export function showDeleteConfirm(message) {
    return showConfirm(message, {
        okTitle: lang('modals.confirm.delete.ok_button'),
        okVariant: 'danger',
    });
}
