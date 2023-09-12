<script setup lang="ts">
    import { nextTick, ref } from "vue";

    defineProps<{}>();

    const stateDropdown = ref();

    async function openStateDropdown() {
        await nextTick();
        stateDropdown.value.open();
    }
</script>

<template>
    <slot v-bind="{
        stateDropdownRef: el => stateDropdown = el,
        openStateDropdown,
    }" />
</template>
