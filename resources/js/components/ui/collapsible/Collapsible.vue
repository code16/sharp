<script setup lang="ts">
import { CollapsibleRoot, useForwardPropsEmits } from 'reka-ui'
import type { CollapsibleRootEmits, CollapsibleRootProps } from 'reka-ui'

const props = defineProps<CollapsibleRootProps>()
const emits = defineEmits<CollapsibleRootEmits>()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <CollapsibleRoot v-slot="{ open }" v-bind="forwarded">
    <slot :open="open" />
  </CollapsibleRoot>
</template>
