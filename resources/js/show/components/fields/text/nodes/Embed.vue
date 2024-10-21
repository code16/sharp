<script setup lang="ts">
    import EmbedRenderer from "@/content/components/EmbedRenderer.vue";
    import { inject, ref, useAttrs } from "vue";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { Show } from "@/show/Show";
    import { EmbedData } from "@/types";
    import { Card, CardContent } from "@/components/ui/card";
    const props = defineProps<{
        embed: EmbedData,
    }>();

    const embedManager = inject<ContentEmbedManager<Show>>('embedManager');
    const embedData = embedManager.getEmbed(useAttrs()['data-key'] as string, props.embed);
</script>

<template>
    <div class="my-4 first:mt-0 last:mb-0 bg-background border rounded-md p-4">
        <component v-if="embedData" :is="embed.tag">
            <EmbedRenderer
                :data="embedData"
                :embed="embed"
            >
                <slot />
            </EmbedRenderer>
        </component>
    </div>
</template>

