<script setup lang="ts">
    import { type HTMLAttributes, computed, ref, watchEffect, watch } from 'vue'
    import {
        ScrollAreaCorner,
        ScrollAreaRoot,
        type ScrollAreaRootProps,
        ScrollAreaViewport,
    } from 'reka-ui'
    import ScrollBar from './ScrollBar.vue'
    import { cn } from '@/utils/cn'
    import { useElementSize, useMediaQuery, useScroll, useThrottleFn } from "@vueuse/core";

    const props = defineProps<ScrollAreaRootProps & { class?: HTMLAttributes['class'], touchType?: ScrollAreaRootProps['type'] }>()

    const delegatedProps = computed(() => {
      const { class: _, ...delegated } = props

      return delegated
    })

    const isTouchscreen = useMediaQuery('(hover: none)');
    const viewport = ref<InstanceType<typeof ScrollAreaViewport>>();
    const { arrivedState, measure } = useScroll(() => viewport.value?.viewportElement);

    watch(
        useElementSize(() => viewport.value?.viewportElement).width,
        useThrottleFn(() => { measure() }, 50, true, true)
    );
</script>

<template>
  <ScrollAreaRoot v-bind="delegatedProps" :type="isTouchscreen ? props.touchType : props.type"
      :class="cn('group/scroll-area relative overflow-hidden', props.class)"
      :data-scroll-arrived-right="arrivedState.right ? true : null"
      :data-scrollbar-x-visible="!arrivedState.left || !arrivedState.right ? true : null"
  >
    <ScrollAreaViewport
        class=" h-full w-full rounded-[inherit]"
        ref="viewport"
    >
      <slot />
    </ScrollAreaViewport>
      <slot name="scrollbar">
          <ScrollBar />
          <ScrollAreaCorner />
      </slot>
  </ScrollAreaRoot>
</template>
