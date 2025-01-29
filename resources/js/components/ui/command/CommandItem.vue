<script setup lang="ts">
    import { type HTMLAttributes, computed, Directive } from 'vue'
    import { ComboboxItemEmits, ComboboxItemProps } from 'reka-ui'
import { ComboboxItem, useForwardPropsEmits } from 'reka-ui'
import { cn } from '@/utils/cn'
    import { vScrollIntoViewOverride } from "@/directives/scroll-into-view";

    const props = withDefaults(defineProps<ComboboxItemProps & { class?: HTMLAttributes['class'], autofocus?: false }>(), {
        autofocus: undefined,
    })
const emits = defineEmits<ComboboxItemEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)

    // radix focuses highlighted item on first render, disable it https://github.com/unovue/radix-vue/blob/v2/packages/core/src/Listbox/ListboxRoot.vue#L163
    const vDisableFirstFocus: Directive<HTMLElement & { _focus: HTMLElement['focus'] }, boolean> = {
        beforeMount(el, { value }) {
            if(value) {
                el._focus = el.focus;
                el.focus = () => { el.focus = el._focus }
            }
        },
    }
</script>

<template>
  <ComboboxItem
    v-bind="forwarded"
    :class="cn('relative flex gap-2 cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[highlighted]:bg-accent data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0', props.class)"
    v-scroll-into-view-override
    v-disable-first-focus="props.autofocus === false"
    ref="el"
  >
    <slot />
  </ComboboxItem>
</template>
