<script setup lang="ts">
    import { LayoutFieldData } from "@/types";

    defineProps<{
        layout: LayoutFieldData,
    }>()
</script>

<template>
    <div class="w-[calc(12/var(--size)*100%)]" :style="{ '--size': `${layout.size}` }">
        <slot />
    </div>
</template>
