<script setup lang="ts">
    import { useElementBounding } from "@vueuse/core";
    import { computed, reactive, ref, watch } from "vue";

    defineProps<{
        stuck?: boolean,
    }>();

    const emit = defineEmits(['update:stuck']);
    const el = ref<HTMLElement>();
    const content = computed<HTMLElement>(() => el.value?.querySelector('[data-sticky-content]'));
    const selfRect = reactive(useElementBounding(el, { updateTiming: 'next-frame' }));
    const contentRect = reactive(useElementBounding(content, { updateTiming: 'next-frame' }));
    const topbarSafeRect = reactive(useElementBounding(() => document.querySelector('[data-topbar-sticky-safe-area]') as HTMLElement, { updateTiming: 'next-frame' }));
    const stuck = computed(() => {
        const style = el.value ? window.getComputedStyle(el.value) : null;
        const { bottom, top } = selfRect;
        return el.value
            && style.position === 'sticky'
            && bottom >= 0
            && top <= parseFloat(style.top);
    });
    const isOverflowing = ref(false);
    watch(selfRect, () => {
        isOverflowing.value = el.value.scrollWidth > el.value.clientWidth;
    });
    watch(stuck, () => {
        emit('update:stuck', stuck.value);
    });
    watch([stuck, contentRect], () => {
        if(content.value) {
            const topBarSafeArea = (document.querySelector('[data-topbar-sticky-safe-area]') as HTMLElement);
            topBarSafeArea.style.minWidth = stuck.value ? `${contentRect.width}px` : 'auto';
        }
    });
</script>

<template>
    <div :style="{
            '--top-bar-height': `${topbarSafeRect.height}px`,
            '--sticky-safe-left-offset': stuck ? `${Math.max(topbarSafeRect.left - selfRect.left, 0)}px` : '0px',
            '--sticky-safe-right-offset': stuck ? `${Math.max(selfRect.right - topbarSafeRect.right - parseInt(window.getComputedStyle(el).paddingRight), 0)}px` : '0px',
        }"
        :data-stuck="stuck ? true : null"
        :data-overflowing="isOverflowing ? true : null"
        ref="el"
    >
        <slot v-bind="{ stuck, largerThanTopbar: selfRect.height > topbarSafeRect.height, isOverflowing }" />
    </div>
</template>
