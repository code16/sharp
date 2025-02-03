<script setup lang="ts">
    import { IconData } from "@/types";
    import { computed } from "vue";

    const props = defineProps<{
        icon: IconData | null,
    }>();

    const svg = computed(() => {
        const renderer = document.createElement('div');
        renderer.innerHTML = props.icon?.svg;
        return renderer.querySelector('svg');
    });
</script>

<template>
    <svg v-bind="Object.fromEntries([...svg?.attributes ?? []].map(attr => [attr.name, attr.value]))"
        aria-hidden="true"
        v-html="svg?.innerHTML ?? '<\!-- Undefined icon -->'"
    ></svg>
</template>

