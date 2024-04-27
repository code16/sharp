<script setup lang="ts">
    import { toReactive, useCurrentElement, useElementBounding } from "@vueuse/core";
    import { computed, watch } from "vue";

    const emit = defineEmits(['update:stuck']);
    const el = useCurrentElement();
    const selfRect = toReactive(useElementBounding(el));
    const topbarSafeRect = toReactive(useElementBounding(() => document.querySelector('[data-topbar-sticky-safe-area]')));
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
        '--sticky-safe-right-offset': mustSafe ? `${Math.max(selfRect.right - topbarSafeRect.right, 0)}px` : '0px',
    }">
        <slot v-bind="{ stuck: mustSafe, largerThanTopbar: selfRect.height > topbarSafeRect.height }" />
    </div>
</template>
