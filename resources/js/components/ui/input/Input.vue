<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { useVModel } from '@vueuse/core'
import { cn } from '@/utils/cn'

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class']
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <input v-model="modelValue" :class="cn('flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:px-1.5 file:py-px file:border-0 file:rounded file:bg-secondary file:text-secondary-foreground file:text-sm file:cursor-pointer file:font-medium file:transition-colors [&[type=file]]:cursor-pointer placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', props.class)">
</template>
