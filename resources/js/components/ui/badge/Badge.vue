<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { type BadgeVariants, badgeVariants } from '.'
import { cn } from '@/utils/cn'
import { Primitive } from "radix-vue";

const props = defineProps<{
  variant?: BadgeVariants['variant']
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <Primitive :class="cn(badgeVariants({ variant }), props.class)">
    <slot />
  </Primitive>
</template>
