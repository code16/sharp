<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/utils/cn'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <td
    :class="
      cn(
        'px-3 py-4 align-middle [&:has([role=checkbox])]:pr-0 first:pl-4 last:pr-4',
        props.class,
      )
    "
  >
    <slot />
  </td>
</template>
