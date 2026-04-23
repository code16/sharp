<script setup lang="ts">
    import { LayoutFieldData } from "@/types";
    import { HTMLAttributes } from "vue";

    defineProps<{
        class?: HTMLAttributes['class'],
        layout?: LayoutFieldData,
    }>();
</script>

<template>
    <div
        class="contents @md/field-container:*:col-[span_var(--size)] @3xl/root-card:*:@md/field-container:col-[span_var(--size)]"
        :style="{ '--size': layout?.size ?? 12 }"
    >
        <slot />
    </div>
</template>
