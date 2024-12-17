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
        class="contents *:col-[span_var(--size)]"
        :style="{ '--size': layout?.size ?? 12 }"
    >
        <slot />
    </div>
</template>
<!--    <div class="px-[calc(var(&#45;&#45;gap-x)/2)] mb-[&#45;&#45;gap-y] w-[calc(var(&#45;&#45;size-xs)/12*100%)] md:w-[calc(var(&#45;&#45;size)/12*100%)]"-->
<!--        :style="{ '&#45;&#45;size': layout?.size ?? 12, '&#45;&#45;size-xs': layout?.sizeXS ?? 12 }"-->
<!--    >-->
<!--        <slot></slot>-->
<!--    </div>-->
