<script lang="ts" setup>
    import { TemplateRenderer } from "@/components";
    import { EmbedData } from "@/types";

    defineProps<{
        data: EmbedData['value'];
        embed: EmbedData,
    }>();
</script>

<template>
    <TemplateRenderer
        name="Embed"
        :template-data="data"
        :template="embed.template"
    >
        <slot>
            <template v-if="data.slot">
                <div v-html="data.slot"></div>
            </template>
        </slot>
    </TemplateRenderer>
</template>
