<script setup lang="ts">
    import { computed, ref, useTemplateRef } from "vue";
    import { useIntersectionObserver, useResizeObserver } from "@vueuse/core";

    defineProps<{
        sentinelClass: string,
    }>()

    const el = useTemplateRef('el');
    const sentinel = useTemplateRef('sentinel');
    const sentinelIntersecting = ref(false);
    const isSticky = ref(false);
    const stuck = computed(() => !sentinelIntersecting.value && isSticky.value);
    useIntersectionObserver(sentinel, (entries) => {
        sentinelIntersecting.value = entries[0].isIntersecting;
    });
    useResizeObserver(document.documentElement, () => {
        isSticky.value = el.value && getComputedStyle(el.value).position === 'sticky';
    });
</script>

<template>
    <div v-bind="$attrs" :data-stuck="stuck ? true : null" ref="el">
        <slot v-bind="{ stuck }" />
    </div>
    <div ref="sentinel" class="absolute inset-x-0" :class="sentinelClass"></div>
</template>
