<script setup lang="ts">
    import { onMounted, onUpdated, ref } from "vue";

    const largestLabelWidth = ref();
    const el = ref<HTMLDivElement>();

    function layout() {
        largestLabelWidth.value = 0;
        el.value.style.removeProperty('--label-width');
        el.value.querySelectorAll('[data-filter-label]').forEach((label: HTMLElement) => {
            largestLabelWidth.value = Math.max(largestLabelWidth.value, label.offsetWidth);
        });
        el.value.style.setProperty('--label-width', `${largestLabelWidth.value}px`);
    }

    onMounted(layout);

    onUpdated(layout);
</script>

<template>
    <div class="[&_[data-filter-label]]:w-[--label-width]" ref="el">
        <slot></slot>
    </div>
</template>
