import { reactive, ref, toRef, watch } from "vue";
import { MaybeComputedElementRef, useElementBounding, useWindowSize } from "@vueuse/core";
import { makeOverlayPath } from "@/composables/use-overlay-path/overlay-path";


export function useOverlayPath(element: MaybeComputedElementRef) {
    const highlightElement = toRef(element);
    const highlightElementRect = reactive(useElementBounding(element));
    const overlayPath = ref(null);

    watch([highlightElementRect, ...Object.values(useWindowSize())], () => {
        overlayPath.value =  highlightElement.value ? makeOverlayPath(highlightElementRect) : null;
    }, { immediate: true });

    return { overlayPath, highlightElementRect };
}
