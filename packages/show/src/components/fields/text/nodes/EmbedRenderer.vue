<template>
    <component class="embed" :is="embedOptions.tag">
        <TemplateRenderer
            name="Embed"
            :template-data="embedData"
            :template="embedOptions.template"
        />
    </component>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";
    import { ignoreVueElement } from "sharp";

    export default {
        components: {
            TemplateRenderer,
        },
        props: {
            embedData: Object,
            embedOptions: Object,
        },
        created() {
            ignoreVueElement(this.embedOptions.tag);
        },
    }
</script>
