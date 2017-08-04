<template>
    <div class="SharpWidgetPanel">
        <a :href="link">
            <sharp-template name="WidgetPanel" :template="template" :template-data="value.data"></sharp-template>
        </a>
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
            link:String
        }
    }
</script>