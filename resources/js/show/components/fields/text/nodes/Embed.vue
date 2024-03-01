<script setup lang="ts">
    import EmbedRenderer from "@/content/components/EmbedRenderer.vue";
    import { inject, ref, useAttrs } from "vue";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { Show } from "@/show/Show";
    import { EmbedData } from "@/types";

    const contentEmbedManager = inject<ContentEmbedManager<Show>>('contentEmbedManager');

    const embed = contentEmbedManager.getEmbedConfig(useAttrs()['data-unique-id'] as string);
    const data = ref<EmbedData['value']>();

    async function init() {
        data.value = await contentEmbedManager.getResolvedEmbed(useAttrs()['data-unique-id'] as string);
    }
</script>

<template>
    <component :is="embed.tag" class="embed" component="">
        <EmbedRenderer
            :data="data"
            :embed="embed"
        >
            <slot />
        </EmbedRenderer>
    </component>
</template>

