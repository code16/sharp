<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { DropdownMenuItem, type DropdownMenuItemProps, useForwardProps } from 'reka-ui'
import { cn } from '@/utils/cn'

const props = defineProps<DropdownMenuItemProps & { class?: HTMLAttributes['class'], inset?: boolean }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <DropdownMenuItem
    v-bind="forwardedProps"
    :class="cn(
      'relative flex gap-2 cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      inset && 'pl-8',
      props.class,
    )"
  >
    <slot />
  </DropdownMenuItem>
</template>
