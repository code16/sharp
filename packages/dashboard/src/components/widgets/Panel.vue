<template>
    <div class="SharpWidgetPanel">
        <Template name="WidgetPanel" :template="template" :template-data="value.data" />
    </div>
</template>

<script>
    import { Template } from 'sharp/components';

    export default {
        name:'SharpWidgetPanel',
        components: {
            Template
        },
        props: {
            value: Object,
            template: String,
        }
    }
</script>