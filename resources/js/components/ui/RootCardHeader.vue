<script setup lang="ts">
import StickyTop from "@/components/StickyTop.vue";
import { CardHeader } from "@/components/ui/card";

defineProps<{
    sticky?: boolean
}>()

</script>

<template>
    <StickyTop
        class="group top-[calc(3.5rem-1px)] -mt-px -m-px mb-0 z-10 !px-0"
        :class="sticky ? 'data-[overflowing-viewport]:sticky' : ''"
    >
        <CardHeader class="px-4 @3xl:px-6 relative border-t group-data-[stuck]:rounded-none @[87rem]:border-x @[87rem]:rounded-t-lg group-data-[stuck]:shadow-b-md transition-shadow border-b-0 backdrop-blur bg-background/90 backdrop-filter">
            <slot />
        </CardHeader>
    </StickyTop>
</template>
