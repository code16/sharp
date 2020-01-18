<template>
    <div class="SharpWidgetPanel">
        <TemplateRenderer name="WidgetPanel" :template="template" :template-data="value.data" />
    </div>
</template>

<script>
    import { TemplateRenderer } from 'sharp/components';

    export default {
        name:'SharpWidgetPanel',
        components: {
            TemplateRenderer
        },
        props: {
            value: Object,
            template: String,
        }
    }
</script>