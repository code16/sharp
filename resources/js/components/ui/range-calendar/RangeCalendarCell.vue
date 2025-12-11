<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import {
    injectRangeCalendarRootContext,
    RangeCalendarCell,
    type RangeCalendarCellProps,
    useForwardProps
} from 'reka-ui'
import { cn } from '@/utils/cn'
import { isSameMonth, DateValue } from '@internationalized/date';

const props = defineProps<RangeCalendarCellProps & { class?: HTMLAttributes['class'], month: DateValue, monthIndex: number }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
const rootContext = injectRangeCalendarRootContext();
const isNextMonthOutsideSelected = computed(() =>  isSameMonth(props.date, props.month.add({ months: 1 }))
    && rootContext.startValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) <= 0
    && rootContext.endValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) > 0);
const isPreviousMonthOutsideSelected = computed(() => isSameMonth(props.date, props.month.subtract({ months: 1 }))
    && rootContext.startValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) < 0
    && rootContext.endValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) >= 0);
</script>

<template>
  <RangeCalendarCell
    :class="cn('relative p-0 text-center text-sm focus-within:relative focus-within:z-20 [&:has([data-selected][data-selection-end]:not([data-outside-view]))]:rounded-r-md [&:has([data-selected][data-selection-start]:not([data-outside-view]))]:rounded-l-md',
        rootContext.isSelected(props.date) ? 'bg-accent' : '',
        isNextMonthOutsideSelected ? 'bg-accent last:bg-transparent last:bg-linear-to-r last:from-accent last:to-transparent' : '',
        isPreviousMonthOutsideSelected ? 'bg-accent first:bg-transparent first:bg-linear-to-l first:from-accent first:to-transparent' : '',
        props.class
    )"
      v-bind="forwardedProps"
  >
    <slot />
  </RangeCalendarCell>
</template>
