<script setup lang="ts">
    const props = defineProps<{
        icon: { svg: string }
    }>();

    const renderer = document.createElement('div');
    renderer.innerHTML = props.icon.svg;
    const svg = renderer.querySelector('svg');
</script>

<template>
    <svg fill="currentColor" :viewBox="svg.getAttribute('viewBox')" aria-hidden="true" v-html="svg.innerHTML"></svg>
</template>

