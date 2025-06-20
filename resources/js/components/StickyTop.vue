<script setup lang="ts">
    import { useElementBounding, useEventListener, useWindowSize } from "@vueuse/core";
    import { computed, reactive, ref, watch, watchEffect } from "vue";

    defineProps<{}>();

    const el = ref<HTMLElement>();
    const containerRect = reactive(useElementBounding(() => el.value?.parentElement, { updateTiming: 'next-frame' }));
    const selfRect = reactive(useElementBounding(el, { updateTiming: 'next-frame' }));
    const topbarSafeRect = reactive(useElementBounding(() => document.querySelector('[data-topbar-sticky-safe-area]') as HTMLElement, { updateTiming: 'next-frame' }));
    const stuck = computed(() => {
        const style = el.value ? window.getComputedStyle(el.value) : null;
        const { bottom, top } = selfRect;
        const round = (num:number) => Math.round((num + Number.EPSILON) * 100) / 100;
        return el.value
            && style.position === 'sticky'
            && bottom >= 0
            && round(top) <= round(parseFloat(style.top));
    });
    const isOverflowingViewport = ref(false);
    const { height: innerHeight } = useWindowSize();
    watch([containerRect, innerHeight], () => {
        const style = window.getComputedStyle(el.value);
        isOverflowingViewport.value = (parseFloat(style.top) + containerRect.height) > innerHeight.value;
    });

    // Stacked top
    const stackedTop = ref(0);
    const parentStickyTopEl = computed(() => el.value?.closest('[data-sticky-top-container]')?.querySelector('[data-sticky-top]') as HTMLElement);
    const parentStickyTopRect = reactive(useElementBounding(() => parentStickyTopEl.value, { updateTiming: 'next-frame' }));
    function updateStackedTop() {
        const parentStickyStyle = parentStickyTopEl.value ? window.getComputedStyle(parentStickyTopEl.value) : null;
        stackedTop.value = parentStickyTopEl.value && parentStickyStyle.position === 'sticky'
            ? parentStickyTopRect.height + parseFloat(parentStickyStyle.top || '0')
            : topbarSafeRect.height;
    }
    watchEffect(() => {
        updateStackedTop();
    });
    useEventListener(window, 'resize', () => {
        updateStackedTop();
    });
</script>

<template>
    <div :style="{
            '--stacked-top': `${stackedTop}px`
        }"
        :data-stuck="stuck ? true : null"
        :data-overflowing-viewport="isOverflowingViewport ? true : null"
        data-sticky-top
        ref="el"
    >
        <slot v-bind="{ stuck, largerThanTopbar: selfRect.height > topbarSafeRect.height }" />
    </div>
</template>
