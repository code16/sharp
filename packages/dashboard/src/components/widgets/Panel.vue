<template>
    <div class="SharpWidgetPanel">
        <sharp-template name="WidgetPanel" :template="template" :template-data="value.data"></sharp-template>
    </div>
</template>

<script>
    import SharpTemplate from '../../Template';

    export default {
        name:'SharpWidgetPanel',
        components: {
            SharpTemplate
        },
        props: {
            value: Object,
            template: String,
        }
    }
</script>