import { computed, ComputedRef, inject, nextTick, onMounted, provide, ref, Ref } from "vue";
import { injectDialogRootContext } from "reka-ui";

export function provideParentDialogElement(dialog: Ref<HTMLElement>) {
    provide<ComputedRef<HTMLDialogElement | undefined>>(
        'parentDialog',
        computed(() =>
            dialog.value instanceof HTMLDialogElement
                ? dialog.value
                : undefined
        )
    );
}

export function useParentHTMLDialogElement() {
    return inject<ComputedRef<HTMLDialogElement | undefined>>('parentDialog', undefined);
}

export function useParentDialogElement() {
    const parentHTMLDialogElement = useParentHTMLDialogElement();
    const rekaDialogContext = injectDialogRootContext(null);

    if(rekaDialogContext) {
        const rekaDialogElement = ref<HTMLElement | undefined>();

        onMounted(async () => {
            await nextTick();
            rekaDialogElement.value = rekaDialogContext?.contentElement.value?.parentElement;
        });

        return computed(() => parentHTMLDialogElement.value ?? rekaDialogElement.value);
    }

    return parentHTMLDialogElement;
}
