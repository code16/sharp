<script setup lang="ts">
    import { useElementBounding } from "@vueuse/core";
    import { computed, reactive, ref, watch } from "vue";

    defineProps<{
        stuck?: boolean,
    }>();

    const emit = defineEmits(['update:stuck']);
    const el = ref<HTMLElement>();
    const selfRect = reactive(useElementBounding(el));
    const topbarSafeRect = reactive(useElementBounding(() => document.querySelector('[data-topbar-sticky-safe-area]') as HTMLElement));
    const stuck = computed(() => {
        const style = el.value ? window.getComputedStyle(el.value) : null;
        return el.value
            && style.position === 'sticky'
            && selfRect.bottom >= 0
            && selfRect.top <= parseInt(style.top) + 1;
    });
    watch(stuck, () => {
        emit('update:stuck', stuck.value);
    })
</script>

<template>
    <div :style="{
        '--top-bar-height': `${topbarSafeRect.height}px`,
        '--sticky-safe-left-offset': stuck ? `${Math.max(topbarSafeRect.left - selfRect.left, 0)}px` : '0px',
        '--sticky-safe-right-offset': stuck ? `${Math.max(selfRect.right - topbarSafeRect.right - parseInt(window.getComputedStyle(el).paddingRight), 0)}px` : '0px',
    }"
        :data-stuck="stuck"
        ref="el">
        <slot v-bind="{ stuck, largerThanTopbar: selfRect.height > topbarSafeRect.height }" />
    </div>
</template>
