<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import { RangeCalendarPrev, type RangeCalendarPrevProps, useForwardProps } from 'reka-ui'
import { ChevronLeft } from 'lucide-vue-next'
import { cn } from '@/utils/cn'
import { buttonVariants } from '@/components/ui/button'

const props = defineProps<RangeCalendarPrevProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <RangeCalendarPrev
    :class="cn(
      buttonVariants({ variant: 'outline' }),
      'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
      props.class,
    )"
    v-bind="forwardedProps"
  >
    <slot>
      <ChevronLeft class="h-4 w-4" />
    </slot>
  </RangeCalendarPrev>
</template>
