<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { ScrollAreaScrollbar, type ScrollAreaScrollbarProps, ScrollAreaThumb } from 'radix-vue'
import { cn } from '@/utils/cn'

const props = withDefaults(defineProps<ScrollAreaScrollbarProps & { class?: HTMLAttributes['class'] }>(), {
  orientation: 'vertical',
})

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})
</script>

<template>
  <ScrollAreaScrollbar
    v-bind="delegatedProps"
    :class="
      cn('flex touch-none select-none transition-colors',
         orientation === 'vertical'
           && 'h-full w-2.5 border-l border-l-transparent p-px',
         orientation === 'horizontal'
           && 'h-4 flex-col border-t border-t-transparent p-1',
         props.class)"
  >
    <ScrollAreaThumb class="relative flex-1 rounded-full bg-muted-foreground/50 hover:bg-muted-foreground">
        <div class="absolute -inset-1.5 -top-4"></div>
    </ScrollAreaThumb>
  </ScrollAreaScrollbar>
</template>
