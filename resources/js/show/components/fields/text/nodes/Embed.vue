<script setup lang="ts">
    import { inject, ref, useAttrs } from "vue";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { Show } from "@/show/Show";
    import { EmbedData } from "@/types";
    const props = defineProps<{
        embed: EmbedData,
    }>();

    const embedManager = inject<ContentEmbedManager<Show>>('embedManager');
    const embedData = embedManager.getEmbed(useAttrs()['data-key'] as string, props.embed);
</script>

<template>
    <div class="my-4 first:mt-0 last:mb-0 bg-background border rounded-md p-4">
        <component
            v-if="embedData"
            :is="embed.tag"
            v-html="embedData._html"
        />
    </div>
</template>

