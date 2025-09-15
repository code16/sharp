<script setup lang="ts">

import { GripVertical } from "lucide-vue-next";
</script>

<template>
    <div class="z-5 absolute grid place-content-center opacity-0 right-0 top-1/2 translate-x-1/2 -translate-y-1/2 h-4 w-3 rounded-sm border bg-border duration-300 transition-opacity cursor-grab hover:bg-foreground hover:border-foreground hover:text-background in-[[data-node-view-wrapper]:hover]:opacity-100"
        data-drag-handle
    >
        <div class="absolute -inset-x-2 -inset-y-3"></div>
        <GripVertical class="h-2.5 w-2.5" />
    </div>
</template>
