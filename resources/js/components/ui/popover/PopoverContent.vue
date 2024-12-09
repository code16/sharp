<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import {
    injectDialogRootContext,
    PopoverContent,
    type PopoverContentEmits,
    type PopoverContentProps,
    PopoverPortal,
    useForwardPropsEmits,
} from 'reka-ui'
import { cn } from '@/utils/cn'

defineOptions({
  inheritAttrs: false,
})

const props = withDefaults(
  defineProps<PopoverContentProps & { class?: HTMLAttributes['class'] }>(),
  {
    align: 'center',
    sideOffset: 4,
    positionStrategy: 'absolute'
  },
)
const emits = defineEmits<PopoverContentEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const dialogContext = injectDialogRootContext(null);
const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <PopoverPortal :to="dialogContext?.contentElement.value?.parentElement">
    <PopoverContent
      v-bind="{ ...forwarded, ...$attrs }"
      :class="
        cn(
          'z-50 w-72 rounded-md border bg-popover p-4 text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
          // 'max-h-[--reka-popover-content-available-height] overflow-auto',  // on mobile it may cause double overflow e. Filterselect
          props.class,
        )
      "
    >
      <slot />
    </PopoverContent>
  </PopoverPortal>
</template>
