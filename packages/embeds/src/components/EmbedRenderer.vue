<template>
    <TemplateRenderer
        name="Embed"
        :template-data="data"
        :template-props="options.attributes"
        :template="options.template"
    >
        <template v-if="data.slot">
            <div v-html="data.slot"></div>
        </template>
    </TemplateRenderer>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";

    export default {
        components: {
            TemplateRenderer,
        },
        props: {
            data: Object,
            options: Object,
        },
    }
</script>
