<script setup lang="ts">
    import { EntityStateValueData } from "@/types";

    defineProps<{
        stateValue: EntityStateValueData | null,
    }>();
</script>

<template>
    <div class="relative rounded-full" :style="{ '--color': stateValue?.color ?? 'black' }">
<!--        <div class="absolute inset-0 rounded-full bg-[var(&#45;&#45;color)] opacity-10"></div>-->
        <div class="h-1.5 w-1.5 rounded-full bg-(--color)"></div>
    </div>
</template>
