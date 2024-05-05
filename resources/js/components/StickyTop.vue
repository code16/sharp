<script setup lang="ts">
    import { throttledRef, throttledWatch, useElementBounding, watchThrottled } from "@vueuse/core";
    import { computed, reactive, ref, watch } from "vue";

    defineProps<{
        stuck?: boolean
    }>();

    // function throttled<T>(rect: T): T {
    //     const t = reactive({ ...rect });
    //     watchThrottled(rect, (r) => Object.assign(t, r), { throttle: 200 });
    //     return t;
    // }

    const emit = defineEmits(['update:stuck']);
    const el = ref<HTMLElement>();
    const selfRect = reactive(useElementBounding(el));
    const topbarSafeRect = reactive(useElementBounding(() => document.querySelector('[data-topbar-sticky-safe-area]')));
    const mustSafe = computed(() => {
        return selfRect.bottom >= 0 && selfRect.top < topbarSafeRect.bottom;
    });
    watch(mustSafe, () => {
        emit('update:stuck', mustSafe.value);
    })
</script>

<template>
    <div :style="{
        '--sticky-safe-left-offset': mustSafe ? `${Math.max(topbarSafeRect.left - selfRect.left, 0)}px` : '0px',
        '--sticky-safe-right-offset': mustSafe ? `${Math.max(selfRect.right - topbarSafeRect.right - parseInt(window.getComputedStyle(el).paddingRight), 0)}px` : '0px',
    }" ref="el">
        <slot v-bind="{ stuck: mustSafe, largerThanTopbar: selfRect.height > topbarSafeRect.height }" />
    </div>
</template>
