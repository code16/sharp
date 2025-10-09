<script setup lang="ts">

import { GripVertical } from "lucide-vue-next";
import { useCurrentElement, useEventListener, useMutationObserver } from "@vueuse/core";
import { computed, nextTick, onMounted } from "vue";

const el = useCurrentElement<HTMLElement>();
const nodeViewEl = computed<HTMLElement>(() => el.value?.closest('[data-node-view-wrapper]'));

useMutationObserver(nodeViewEl, () => {
    if(nodeViewEl.value.draggable && !nodeViewEl.value.hasAttribute('data-dragging-handle')) {
        nodeViewEl.value.removeAttribute('draggable');
    }
}, { attributes: true });

function onMouseDown(e) {
    nodeViewEl.value.setAttribute('data-dragging-handle', 'true');
    nodeViewEl.value.draggable = true;
}

useEventListener('mouseup', () => {
    if(nodeViewEl.value.hasAttribute('data-dragging-handle')) {
        nodeViewEl.value.removeAttribute('data-dragging-handle');
    }
});

onMounted(() => {
    nextTick(() => {
        nodeViewEl.value.removeAttribute('draggable');
    });
});

useEventListener(nodeViewEl, 'dragstart', (e) => {
    if(!nodeViewEl.value.hasAttribute('data-dragging-handle')) {
        e.preventDefault();
    }
});
</script>

<template>
    <div class="z-5 absolute grid place-content-center opacity-0 right-0 top-1/2 translate-x-1/2 -translate-y-1/2 h-4 w-3 rounded-sm border bg-border duration-300 transition-opacity cursor-grab hover:bg-foreground hover:border-foreground hover:text-background in-[[data-node-view-wrapper]:hover]:opacity-100"
        data-drag-handle
        @mousedown="onMouseDown"
    >
        <div class="absolute -inset-x-2 -inset-y-3"></div>
        <GripVertical class="h-2.5 w-2.5" />
    </div>
</template>
