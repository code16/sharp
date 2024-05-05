<script setup lang="ts">
    import { type HTMLAttributes, computed, reactive, ref } from 'vue'
import {
  AlertDialogContent,
  type AlertDialogContentEmits,
  type AlertDialogContentProps,
  AlertDialogOverlay,
  AlertDialogPortal,
  useForwardPropsEmits,
} from 'radix-vue'
import { cn } from '@/utils/cn'
    import {  useElementBounding } from "@vueuse/core";
    import { useOverlayPath } from "@/composables/use-overlay-path/useOverlayPath";

const props = defineProps<AlertDialogContentProps & { class?: HTMLAttributes['class'], highlightElement? }>()
const emits = defineEmits<AlertDialogContentEmits>()

const delegatedProps = computed(() => {
  const { class: _, highlightElement, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)


    const content = ref<HTMLElement>();
    const contentRect = reactive(useElementBounding(content));
    const { overlayPath, highlightElementRect } = useOverlayPath(props.highlightElement);
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
</script>

<template>
  <AlertDialogPortal>
    <AlertDialogOverlay
      class="fixed inset-0 z-50 bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
        :style="{ 'clip-path': overlayPath ? `path('${overlayPath}')` : null }"
    />
    <AlertDialogContent
      v-bind="forwarded"
      :class="
        cn(
          'fixed left-1/2 top-1/2 z-[70] grid w-full max-w-lg -translate-x-1/2 -translate-y-1/2 gap-4 border bg-background p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%] sm:rounded-lg',
          props.class,
        )
      "
        :style="{
            'translate' : overlayPath ? `0 ${safeTransformY}px` : '',
        }"
        ref="content"
    >
      <slot />
    </AlertDialogContent>
  </AlertDialogPortal>
</template>
