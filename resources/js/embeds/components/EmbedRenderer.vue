<script lang="ts" setup>
    import { TemplateRenderer } from "@/components";
    import { EmbedAttributesData } from "@/form/components/fields/editor/extensions/embed/Embed";
    import { EmbedData } from "@/types";

    defineProps<{
        data: EmbedAttributesData;
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
