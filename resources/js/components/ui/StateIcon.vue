<script setup lang="ts">
    import { EntityStateValueData } from "@/types";

    defineProps<{
        stateValue: EntityStateValueData | null,
    }>();
</script>

<template>
    <div class="relative rounded-full" :style="{ '--color': stateValue?.color ?? 'black' }"
        style="--border: oklch(from var(--color) .9 min(calc(c * .2), 0.03) h);"
    >
<!--        <div class="absolute inset-0 rounded-full bg-[var(&#45;&#45;color)] opacity-10"></div>-->
        <div class="h-1 w-1 rounded-full bg-(--color) outline outline-(--border)"></div>
    </div>
</template>
