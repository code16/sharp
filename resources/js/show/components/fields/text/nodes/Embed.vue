<script setup lang="ts">
    import { inject, ref, useAttrs } from "vue";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { Show } from "@/show/Show";
    import { EmbedData } from "@/types";
    import EmbedHeader from "@/components/EmbedHeader.vue";
    const props = defineProps<{
        embed: EmbedData,
    }>();

    const embedManager = inject<ContentEmbedManager<Show>>('embedManager');
    const embedData = embedManager.getEmbed(props.embed, useAttrs()['data-key'] as string);
</script>

<template>
    <component :is="embed.tag" class="block my-4 first:mt-0 last:mb-0 bg-background border rounded-md p-4 max-w-[600px]">
        <template v-if="embed.displayEmbedHeader">
            <EmbedHeader :embed="embed" />
        </template>
        <template v-if="embedData?._html">
            <div v-html="embedData._html"></div>
        </template>
    </component>
</template>

