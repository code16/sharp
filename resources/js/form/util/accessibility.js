

export function onLabelClicked(vm, id, handler) {
    if(!id) {
        return
    }
    const label = document.querySelector(`label[for="${id}"]`);
    if(label) {
        label.addEventListener('click', handler);
        vm.$on('hook:beforeDestroy', () => {
            label.removeEventListener('click', handler);
        });
    }
}
