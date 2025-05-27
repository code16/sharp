<script setup lang="ts">
    import { Badge } from "@/components/ui/badge";
    import { EntityStateValueData } from "@/types";
    import StateIcon from "@/components/ui/StateIcon.vue";

    defineProps<{
        stateValue: EntityStateValueData | null,
    }>();
</script>

<template>
    <Badge class="text-(--foreground) border-(--border) bg-(--background) hover:bg-(--background) in-[button[aria-expanded=true]]:border-(--border-hover) in-[button:hover]:border-(--border-hover) hover:border-(--border-hover)"
        style="
            --foreground: oklch(from var(--color) .35 min(c, 0.1) h);
            --background: oklch(from var(--color) max(l, .97) min(calc(c * .2), 0.03) h);
            --border: oklch(from var(--color) .9 min(calc(c * .2), 0.03) h);
            --border-hover: oklch(from var(--border) .7 min(calc(c * 5), 0.05) h);
        "
        :style="{
            '--color': stateValue?.color ?? 'black',
        }"
    >
        <StateIcon class="-ml-0.5 mr-1.5" :state-value="stateValue" />
        <slot></slot>
    </Badge>
</template>
