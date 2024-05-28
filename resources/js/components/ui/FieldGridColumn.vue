<script setup lang="ts">
    import { LayoutFieldData } from "@/types";
    import { Primitive, Slot } from "radix-vue";

    defineProps<{
        layout?: LayoutFieldData,
    }>();
</script>

<template>
    <Primitive
        as-child
        class="col-[span_var(--size-xs)] md:col-[span_var(--size)]"
        :style="{ '--size-xs': layout?.sizeXS ?? 12, '--size': layout?.size ?? 12 }"
    >
        <slot />
    </Primitive>
</template>
<!--    <div class="px-[calc(var(&#45;&#45;gap-x)/2)] mb-[&#45;&#45;gap-y] w-[calc(var(&#45;&#45;size-xs)/12*100%)] md:w-[calc(var(&#45;&#45;size)/12*100%)]"-->
<!--        :style="{ '&#45;&#45;size': layout?.size ?? 12, '&#45;&#45;size-xs': layout?.sizeXS ?? 12 }"-->
<!--    >-->
<!--        <slot></slot>-->
<!--    </div>-->
