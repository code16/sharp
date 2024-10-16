<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/utils/cn'

const props = defineProps<{
  class?: HTMLAttributes['class'],
  noScroll?: boolean,
}>()
</script>

<template>
  <div class="relative" :class="{ 'w-full overflow-auto': !noScroll }">
    <table :class="cn('w-full caption-bottom text-sm', props.class)">
      <slot />
    </table>
  </div>
</template>
