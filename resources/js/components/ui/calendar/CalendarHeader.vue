<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import { CalendarHeader, type CalendarHeaderProps, useForwardProps } from 'reka-ui'
import { cn } from '@/utils/cn'

const props = defineProps<CalendarHeaderProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <CalendarHeader :class="cn('relative flex w-full items-center justify-between pt-1', props.class)" v-bind="forwardedProps">
    <slot />
  </CalendarHeader>
</template>
