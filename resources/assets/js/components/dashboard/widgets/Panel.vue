<template>
    <div class="SharpWidgetPanel">
        <sharp-template name="WidgetPanel" :template="template" :template-data="value.data"></sharp-template>
    </div>
</template>

<script>
    import Template from '../../Template';
    export default {
        name:'SharpWidgetPanel',
        components: {
            [Template.name]:Template
        },
        props: {
            value: Object,
            template:String,
        }
    }
</script>