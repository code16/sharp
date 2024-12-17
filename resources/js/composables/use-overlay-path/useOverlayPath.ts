import { computed, reactive, ref, toRef, watch } from "vue";
import { MaybeComputedElementRef, useElementBounding, useWindowSize } from "@vueuse/core";
import { makeOverlayPath } from "@/composables/use-overlay-path/overlay-path";


export function useOverlayPath(element: MaybeComputedElementRef, dialogContent: MaybeComputedElementRef) {
    const highlightElement = toRef(element);
    const highlightElementRect = reactive(useElementBounding(element));
    const contentRect = reactive(useElementBounding(dialogContent));
    const overlayPath = ref(null);

    watch([highlightElementRect, ...Object.values(useWindowSize())], () => {
        overlayPath.value = highlightElement.value ? makeOverlayPath(highlightElementRect) : null;
    }, { immediate: true });

    // translate the dialog content to prevent overlapping the highlighting element
    const safeTransformY = computed(() => {
        if(overlayPath.value) {
            const margin = 40;
            const d1 = highlightElementRect.top - margin - (window.innerHeight / 2 + contentRect.height / 2);
            const d2 = highlightElementRect.bottom + margin - (window.innerHeight / 2 - contentRect.height / 2);
            if(d1 < 0 && d2 > 0 || d1 > 0 && d2 < 0) {
                return Math.min(
                    Math.max(
                        (window.innerHeight / 2) * -1,
                        (Math.abs(d1) < Math.abs(d2) ? d1 : d2)
                    ),
                    window.innerHeight / 2 + contentRect.height
                );
            }
        }
        return 0;
    });

    return {
        overlayPath,
        highlightElementRect,
        safeTransformY,
    };
}
