<template>
    <div class="SharpWidgetPanel">
        <a :href="url">
            <sharp-template name="WidgetPanel" :template="template" :template-data="value"></sharp-template>
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
            url:String
        }
    }
</script>