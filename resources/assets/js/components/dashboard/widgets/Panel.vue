<template>
    <div class="SharpWidgetPanel card">
        <sharp-template name="WidgetPanel" :template="template" :template-data="value"></sharp-template>
    </div>
</template>

<script>
    import Template from '../../Template';
    export default {
        name:'SharpWidgetPanel',
        props: {
            value: Object,
            template:String
        }
    }
</script>