<script setup lang="ts">
    import { Badge } from "@/components/ui/badge";
    import { EntityStateValueData } from "@/types";
    import StateIcon from "@/components/ui/StateIcon.vue";

    defineProps<{
        stateValue: EntityStateValueData | null,
    }>();
</script>

<template>
    <Badge class="text-(--foreground) border-(--border) bg-(--background)"
        style="
            --foreground: oklch(from var(--color) .35 min(c, 0.1) h);
            --background: oklch(from var(--color) max(l, .97) min(calc(c * .2), 0.03) h);
            --border: oklch(from var(--color) .9 min(calc(c * .2), 0.03) h);
        "
        :style="{
            '--color': stateValue?.color ?? 'black',
        }"
    >
        <StateIcon class="-ml-0.5 mr-1.5" :state-value="stateValue" />
        <slot></slot>
    </Badge>
</template>
