<script setup lang="ts">
    import { type Component, computed } from "vue";
    import File from "@/show/components/fields/text/nodes/Upload.vue";
    import Html from "@/show/components/fields/text/nodes/Html.vue";
    import { ShowTextFieldData } from "@/types";
    import Embed from "@/show/components/fields/text/nodes/Embed.vue";
    import { components } from '@/components/TemplateRenderer.vue';

    const props = defineProps<{
        field: ShowTextFieldData,
        content: string,
    }>();

    const formattedContent = computed(() => {
        const dom = document.createElement('template');
        dom.innerHTML = props.content;
        dom.content.querySelectorAll('[data-html-content]').forEach(htmlNode => {
            const component = document.createElement('html-content');
            component.setAttribute('content', htmlNode.innerHTML.trim());
            dom.content.insertBefore(component, htmlNode);
            dom.content.removeChild(htmlNode);
        });
        return dom.innerHTML;
    })

    const component = computed<Component>(() => ({
        template: `<div>${formattedContent.value}</div>`,
        components: {
            ...components,
            'x-sharp-file': File,
            'x-sharp-image': File,
            'html-content': Html,
            ...Object.fromEntries(
                Object.entries(props.field.embeds ?? {})
                    .map(([embedKey, embed]) => [
                        embed.tag,
                        {
                            template: '<Embed :embed="embed" v-bind="$attrs"></Embed>',
                            components: { Embed },
                            data: () => ({ embed }),
                        }
                    ])
            ),
        },
    }));
</script>

<template>
    <component :is="component" />
</template>

