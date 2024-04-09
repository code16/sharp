<script setup lang="ts">
    import { ref } from "vue";

    const slotProps = {
        stateDropdownOpened: ref(false),
    };

    defineSlots<{
        default: (props: typeof slotProps) => any
    }>();
</script>

<template>
    <slot v-bind="slotProps" />
</template>
