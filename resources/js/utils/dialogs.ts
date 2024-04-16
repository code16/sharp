import { __ } from "./i18n";
import { ModalProps } from "@/components/ui/types";
import { ref } from "vue";

type Dialog = {
    id: number,
    open: boolean,
    title?: string,
    text: string,
    okTitle?: string,
    okVariant?: 'destructive',
    isError?: boolean,
    okOnly?: boolean,
    onOk: () => void,
    onHidden: () => void,
}

let modalId = 0;
let dialogs = ref<Dialog[]>([]);

export function useDialogs() {
    return dialogs;
}

export function showDialog(text: string, props: Partial<Dialog> = null) {
    const id = modalId++;

    return new Promise((resolve) => {
        dialogs.value.push({
            id,
            ...props,
            text,
            open: true,
            onOk: () => resolve(true),
            onHidden: () => {
                dialogs.value = dialogs.value.filter(dialog => dialog.id !== id);
                resolve(false);
            },
        });
    });
}

export function showAlert(message: string, props: Partial<Dialog> = null) {
    return showDialog(message, {
        okOnly: true,
        ...props
    });
}

export function showConfirm(message: string, props: Partial<Dialog> = null) {
    return showDialog(message, {
        okTitle: __('sharp::modals.confirm.ok_button'),
        ...props
    });
}

export function showDeleteConfirm(message: string) {
    return showConfirm(message, {
        okTitle: __('sharp::modals.confirm.delete.ok_button'),
        okVariant: 'destructive',
    });
}
