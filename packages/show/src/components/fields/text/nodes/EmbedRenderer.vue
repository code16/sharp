<template>
    <div style="max-width: 500px">
        <TemplateRenderer
            name="Embed"
            :template-data="embedData"
            :template="embedOptions.previewTemplate"
        />
    </div>
</template>

<script>
    import { TemplateRenderer } from "sharp/components";

    export default {
        components: {
            TemplateRenderer,
        },
        props: {
            embedData: Object,
            embedOptions: Object,
        },
    }
</script>
