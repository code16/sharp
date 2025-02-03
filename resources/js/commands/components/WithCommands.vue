<script setup lang="ts">
    import { CommandManager } from "../CommandManager";
    import CommandViewPanel from "./CommandViewPanel.vue";
    import CommandFormModal from "./CommandFormModal.vue";

    defineProps<{
        commands: CommandManager,
    }>();
</script>

<template>
    <slot />

    <CommandViewPanel :commands="commands" />
    <CommandFormModal :commands="commands" />
</template>
