<script lang="ts" setup>
import { type HTMLAttributes, computed } from 'vue'
import {
    injectRangeCalendarRootContext,
    RangeCalendarCell,
    type RangeCalendarCellProps,
    useForwardProps
} from 'reka-ui'
import { cn } from '@/utils/cn'
import { isSameMonth, DateValue, compare } from '@internationalized/date';

const props = defineProps<RangeCalendarCellProps & { class?: HTMLAttributes['class'], month: DateValue, monthIndex: number }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
const rootContext = injectRangeCalendarRootContext();
const currentMonthSelected = computed(() => rootContext.startValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) <= 0
&& rootContext.endValue.value?.set({ day: 0 }).compare(props.month.set({ day: 0 })) >= 0 ? true : null);
</script>

<template>
  <RangeCalendarCell
    :class="cn('relative p-0 text-center text-sm focus-within:relative focus-within:z-20 [&:has([data-selected][data-selection-end]:not([data-outside-view]))]:rounded-r-md [&:has([data-selected][data-selection-start]:not([data-outside-view]))]:rounded-l-md',
    'data-selected:bg-accent',
    '[&[data-selected]:not([data-current-month-selected])]:bg-transparent',
    '[[data-outside-view][data-selected]~&[data-before-next-month]]:bg-accent',
    '[[data-outside-view][data-selected]~&[data-before-next-month]:last-child,&[data-before-next-month][data-selected]:last-child]:bg-transparent',
    '[[data-outside-view][data-selected]~&[data-before-next-month]:last-child,&[data-before-next-month][data-selected]:last-child]:bg-linear-to-r',
    '[[data-outside-view][data-selected]~&[data-before-next-month]:last-child,&[data-before-next-month][data-selected]:last-child]:from-accent',
    '[[data-outside-view][data-selected]~&[data-before-next-month]:last-child,&[data-before-next-month][data-selected]:last-child]:to-transparent',
    '[&[data-after-previous-month]:has(~_[data-outside-view][data-selected])]:bg-accent',
    '[&[data-after-previous-month]:first-child:has(~_[data-outside-view][data-selected]),&[data-after-previous-month][data-selected]:first-child]:bg-transparent',
    '[&[data-after-previous-month]:first-child:has(~_[data-outside-view][data-selected]),&[data-after-previous-month][data-selected]:first-child]:bg-linear-to-l',
    '[&[data-after-previous-month]:first-child:has(~_[data-outside-view][data-selected]),&[data-after-previous-month][data-selected]:first-child]:from-accent',
    '[&[data-after-previous-month]:first-child:has(~_[data-outside-view][data-selected]),&[data-after-previous-month][data-selected]:first-child]:to-transparent',
        props.class
    )"
      :data-selected="rootContext.isSelected(props.date) ? true : null"
      :data-outside-view="!isSameMonth(props.date, props.month) ? true : null"
      :data-current-month-selected="currentMonthSelected"
      :data-before-next-month="currentMonthSelected && isSameMonth(props.date, props.month.add({ months: 1 })) ? true : null"
      :data-after-previous-month="currentMonthSelected && isSameMonth(props.date, props.month.subtract({ months: 1 })) ? true : null"
    v-bind="forwardedProps"
  >
    <slot />
  </RangeCalendarCell>
</template>
