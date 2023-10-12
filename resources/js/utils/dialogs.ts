import { __ } from "./i18n";
import { ModalProps } from "@sharp/ui/src/components/types";
import { ref } from "vue";

let modalId = 0;
let dialogs = ref([]);

export function useDialogs() {
    return dialogs;
}

export function showDialog(text: string, props: ModalProps = {}) {
    const id = modalId++;

    return new Promise((resolve) => {
        dialogs.value.push({
            id,
            props: {
                visible: true,
                maxWidth: 'sm',
                ...props,
            },
            text,
            onOk: () => resolve(true),
            onHidden: () => {
                dialogs.value = dialogs.value.filter(dialog => dialog.id !== id);
                resolve(false);
            },
        });
    });
}

export function showAlert(message: string, props: ModalProps = {}) {
    return showDialog(message, {
        okOnly: true,
        ...props
    });
}

export function showConfirm(message: string, props: ModalProps = {}) {
    return showDialog(message, {
        okTitle: __('sharp::modals.confirm.ok_button'),
        ...props
    });
}

export function showDeleteConfirm(message: string) {
    return showConfirm(message, {
        okTitle: __('sharp::modals.confirm.delete.ok_button'),
        okVariant: 'danger',
    });
}
