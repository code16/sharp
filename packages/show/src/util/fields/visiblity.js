

export function syncVisibility(vm, getter, { lazy } = {}) {
    vm.$watch(getter, visible => {
        vm.$emit('visible-change', visible);
    }, { immediate: !lazy });
}