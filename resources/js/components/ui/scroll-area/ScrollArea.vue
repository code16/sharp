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

    watch(useElementSize(() => viewport.value?.viewportElement).width, useThrottleFn(measure));
</script>

<template>
  <ScrollAreaRoot v-bind="delegatedProps" :type="isTouchscreen ? touchType : type" :class="cn('relative overflow-hidden', props.class)">
    <ScrollAreaViewport class="group/viewport h-full w-full rounded-[inherit]" :data-scroll-arrived-right="arrivedState.right" ref="viewport">
      <slot />
    </ScrollAreaViewport>
    <ScrollBar />
    <ScrollAreaCorner />
  </ScrollAreaRoot>
</template>
