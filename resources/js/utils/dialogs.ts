import { __ } from "./i18n";
import { ref, UnwrapNestedRefs } from "vue";
import type { MaybeComputedElementRef } from "@vueuse/core";

export type RootAlertDialog = {
    id: number,
    open: boolean,
    title?: string,
    text: string,
    okTitle?: string,
    okVariant?: 'destructive',
    isError?: boolean,
    isHtmlContent?: boolean,
    okOnly?: boolean,
    onOk: () => void,
    onHidden: () => void,
    highlightElement?: MaybeComputedElementRef,
}

let modalId = 0;
let dialogs = ref<RootAlertDialog[]>([]);

export function useDialogs() {
    return dialogs;
}

export function showDialog(text: string, props: Partial<RootAlertDialog> = null) {
    const id = modalId++;

    return new Promise((resolve) => {
        dialogs.value.push({
            id,
            ...(props as UnwrapNestedRefs<RootAlertDialog>),
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

export function showAlert(message: string, props: Partial<RootAlertDialog> = null) {
    return showDialog(message, {
        okOnly: true,
        ...props
    });
}

export function showConfirm(message: string, props: Partial<RootAlertDialog> = null) {
    return showDialog(message, {
        ...props,
        title: props?.title ?? __('sharp::modals.confirm.title'),
        okTitle: props?.okTitle ?? __('sharp::modals.confirm.ok_button'),
    });
}

export function showDeleteConfirm(message: string, props: Partial<RootAlertDialog> = null) {
    return showConfirm(message, {
        title: __('sharp::modals.confirm.delete.title'),
        okTitle: __('sharp::modals.confirm.delete.ok_button'),
        okVariant: 'destructive',
        ...props,
    });
}
