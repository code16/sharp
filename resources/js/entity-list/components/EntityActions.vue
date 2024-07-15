<script setup lang="ts">
    import { ref, watchEffect } from "vue";

    const menuOpened = ref(false);
    const stateSubmenuOpened = ref(false);
    const requestedStateMenu = ref(false);

    let timeout = null;
    const slotProps = {
        menuOpened,
        stateSubmenuOpened,
        requestedStateMenu,
        openStateMenu() {
            clearTimeout(timeout);
            requestedStateMenu.value = true;
            menuOpened.value = true;
            setTimeout(() => stateSubmenuOpened.value = true, 50);
        },
    };

    watchEffect(() => {
        if(!menuOpened.value) {
            timeout = setTimeout(() => requestedStateMenu.value = false, 100);
        }
    });

    defineSlots<{
        default: (props: typeof slotProps) => any
    }>();
</script>

<template>
    <slot v-bind="slotProps" />
</template>
