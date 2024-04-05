<script setup lang="ts">
    import { ref } from "vue";

    const stateDropdownOpened = ref(false);
</script>

<template>
    <slot v-bind="{
        stateDropdownOpened,
    }" />
</template>
