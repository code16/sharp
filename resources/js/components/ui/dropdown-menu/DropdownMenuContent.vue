<script setup lang="ts">
    import { type HTMLAttributes, computed } from 'vue'
import {
  DropdownMenuContent,
  type DropdownMenuContentEmits,
  type DropdownMenuContentProps,
  DropdownMenuPortal,
  useForwardPropsEmits,
} from 'reka-ui'
import { cn } from '@/utils/cn'
import { useMenuBoundaryElement } from "@/Layouts/Layout.vue";

const props = withDefaults(
  defineProps<DropdownMenuContentProps & { class?: HTMLAttributes['class'] }>(),
  {
    sideOffset: 4,
    positionStrategy: 'absolute',
  },
)
const emits = defineEmits<DropdownMenuContentEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
const menuBoundary = useMenuBoundaryElement();
</script>

<template>
  <DropdownMenuPortal>
    <DropdownMenuContent
      v-bind="forwarded"
      :collision-boundary="typeof props.collisionBoundary === 'undefined' ? menuBoundary : props.collisionBoundary"
      :class="cn('z-50 min-w-24 max-w-(--reka-dropdown-menu-content-available-width) sm:min-w-32 overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2', props.class)"
    >
      <slot />
    </DropdownMenuContent>
  </DropdownMenuPortal>
</template>
