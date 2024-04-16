<script setup lang="ts">
    import EmbedRenderer from "@/content/components/EmbedRenderer.vue";
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
    <component v-if="embedData" :is="embed.tag" class="embed">
        <EmbedRenderer
            :data="embedData"
            :embed="embed"
        >
            <slot />
        </EmbedRenderer>
    </component>
</template>

