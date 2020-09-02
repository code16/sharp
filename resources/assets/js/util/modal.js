import Vue from 'vue';

let modalId = 0;
const state = Vue.observable({
    mainModals: [],
});

export function mainModals() {
    return state.mainModals;
}

export function showMainModal({ text, okCallback = ()=>{}, okCloseOnly, isError, ...props }) {
    const id = modalId++;
    function hiddenCallback() {
        state.mainModals = state.mainModals.filter(dialog => dialog.id !== id);
    }
    state.mainModals.push({
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
    })
}

export function showAlert(message, { title, ...props } = {}) {
    return showMainModal({
        okCloseOnly: true,
        text: message,
        title,
        ...props
    });
}

export function showConfirm(message, { title, ...props } = {}) {
    return showMainModal({
        text: message,
        title,
        ...props
    });
}