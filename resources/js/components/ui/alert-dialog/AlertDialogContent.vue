<script setup lang="ts">
    import { type HTMLAttributes, computed,  ref } from 'vue'
import {
  AlertDialogContent,
  type AlertDialogContentEmits,
  type AlertDialogContentProps,
  AlertDialogOverlay,
  AlertDialogPortal,
  useForwardPropsEmits,
} from 'reka-ui'
import { cn } from '@/utils/cn'
    import { useOverlayPath } from "@/composables/use-overlay-path/useOverlayPath";

    const props = defineProps<AlertDialogContentProps & { class?: HTMLAttributes['class'], highlightElement? }>()
const emits = defineEmits<AlertDialogContentEmits>()

const delegatedProps = computed(() => {
  const { class: _, highlightElement, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)

    const content = ref<HTMLElement>();
    const { overlayPath, safeTransformY } = useOverlayPath(props.highlightElement, content);
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
          'fixed left-1/2 top-1/2 z-70 grid w-full max-w-lg -translate-x-1/2 -translate-y-1/2 gap-4 border bg-background p-6 shadow-lg duration-200 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 sm:rounded-lg',
          // 'data-[state=closed]:slide-out-to-left-1/2 data-[state=closed]:slide-out-to-top-[48%] data-[state=open]:slide-in-from-left-1/2 data-[state=open]:slide-in-from-top-[48%]',
          props.class,
        )
      "
        :style="{
            '--tw-translate-y' : overlayPath ? `calc(-50% + ${safeTransformY}px)` : null,
        }"
    >
      <div class="absolute inset-0 pointer-events-none" aria-hidden="true" ref="content"></div>
      <slot />
    </AlertDialogContent>
  </AlertDialogPortal>
</template>
