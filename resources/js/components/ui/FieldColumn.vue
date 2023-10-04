<script setup lang="ts">
    import { LayoutFieldData } from "@/types";

    defineProps<{
        layout: LayoutFieldData,
    }>()
</script>

<template>
    <div class="w-[calc(var(--size-xs)/12*100%)] md:w-[calc(var(--size)/12*100%)]"
        :style="{ '--size': `${layout.size}`, '--size-xs': `${layout.sizeXS}` }"
    >
        <slot />
    </div>
</template>
