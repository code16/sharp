<script setup lang="ts">
    import { EntityStateValueData } from "@/types";

    defineProps<{
        stateValue: EntityStateValueData | null,
    }>();
</script>

<template>
    <div class="relative rounded-full p-1" :style="{ '--color': stateValue?.color }">
        <div class="absolute inset-0 rounded-full bg-[var(--color,black)] opacity-10"></div>
        <div class="h-1.5 w-1.5 rounded-full bg-[var(--color,black)]"></div>
    </div>
</template>
