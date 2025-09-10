import { computed, nextTick, onMounted, ref } from "vue";
import { injectDialogRootContext } from "reka-ui";



/**
 * Ensure that poppers + dialogs are a child of another parent dialog element for correct positioning / z-index
 */
export function useParentDialogElement() {
    const rekaDialogContext = injectDialogRootContext(null);
    const rekaDialogElement = ref<HTMLElement | undefined>();

    onMounted(async () => {
        await nextTick();
        rekaDialogElement.value = rekaDialogContext?.contentElement.value?.parentElement;
    });

    return computed(() => rekaDialogElement.value);
}
