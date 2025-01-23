<script setup lang="ts">
    import { EmbedData } from "@/types";
    import Icon from "@/components/ui/Icon.vue";

    defineProps<{
        embed: EmbedData,
    }>()
</script>

<template>
    <div class="flex mb-3 -mt-0.5 items-center gap-2 only:my-0" data-embed-title>
        <template v-if="embed.icon">
            <Icon class="size-3 text-accent-foreground" :icon="embed.icon" />
        </template>
        <h3 class="flex-1 min-w-0 truncate text-xs font-medium text-accent-foreground">
            {{ embed.embedHeaderTitle || embed.label }}
        </h3>
    </div>
</template>
