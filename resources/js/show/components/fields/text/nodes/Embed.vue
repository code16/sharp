<script setup lang="ts">
    import EmbedRenderer from "@/content/components/EmbedRenderer.vue";
    import { inject, ref, useAttrs } from "vue";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { Show } from "@/show/Show";
    import { EmbedData } from "@/types";

    const embedManager = inject<ContentEmbedManager<Show>>('embedManager');

    const embed = embedManager.getEmbedConfig(useAttrs()['data-unique-id'] as string);
    const data = ref<EmbedData['value']>();

    async function init() {
        data.value = await embedManager.getResolvedEmbed(useAttrs()['data-unique-id'] as string);
    }

    init();
</script>

<template>
    <component v-if="data" :is="embed.tag" class="embed">
        <EmbedRenderer
            :data="data"
            :embed="embed"
        >
            <slot />
        </EmbedRenderer>
    </component>
</template>

