<script setup lang="ts">
    import { type HTMLAttributes, computed, useTemplateRef } from 'vue'
    import {
        DialogClose,
        DialogContent,
        type DialogContentEmits,
        type DialogContentProps,
        DialogOverlay,
        DialogPortal,
        useForwardPropsEmits,
    } from 'reka-ui'
    import { X } from 'lucide-vue-next'
    import { cn } from '@/utils/cn'
    import { useParentHTMLDialogElement } from "@/composables/useParentDialogElement";

    const props = defineProps<DialogContentProps & { class?: HTMLAttributes['class'] }>()
    const emits = defineEmits<DialogContentEmits>()

    const delegatedProps = computed(() => {
      const { class: _, ...delegated } = props

      return delegated
    })

    const forwarded = useForwardPropsEmits(delegatedProps, emits)
    const overlay = useTemplateRef<InstanceType<typeof DialogOverlay>>('overlay');
    const parentHTMLDialogElement = useParentHTMLDialogElement();
    defineExpose({
        scrollToTop: () => {
            overlay.value.$el.scrollTo(0, 0);
        },
    });
</script>

<template>
  <DialogPortal :to="parentHTMLDialogElement ?? undefined">
    <DialogOverlay
      class="fixed inset-0 z-50 grid grid-cols-1 place-items-center overflow-y-auto bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"
        ref="overlay"
    >
      <DialogContent
        :class="
          cn(
            'relative z-50 grid grid-cols-1 w-full max-w-lg my-8 gap-4 border border-border bg-background p-6 shadow-lg duration-200 sm:rounded-lg md:w-full',
            props.class,
          )
        "
        v-bind="forwarded"
        @pointer-down-outside="(event) => {
          const originalEvent = event.detail.originalEvent;
          const target = originalEvent.target as HTMLElement;
          if (originalEvent.offsetX > target.clientWidth || originalEvent.offsetY > target.clientHeight) {
            event.preventDefault();
          }
        }"
      >
        <slot />

        <DialogClose
          class="absolute top-0.5 right-0.5 p-3 transition-colors rounded-md hover:bg-secondary"
        >
          <X class="w-4 h-4" />
          <span class="sr-only">Close</span>
        </DialogClose>
      </DialogContent>
    </DialogOverlay>
  </DialogPortal>
</template>
