<template>
    <div class="card">
        <div class="card-block">
            <component :is="widgetComp" v-bind="exposedProps"></component>
        </div>
    </div>
</template>
<script>
    import Widgets, { NameAssociation as widgetCompName } from './widgets/index';

    export default {
        name:'SharpWidget',
        components:Widgets,

        props: {
            widgetType:String,
            widgetProps:Object,
            value:Object
        },

        computed: {
            widgetComp() {
                return widgetCompName[this.widgetType]
            },
            exposedProps() {
                return { ...this.widgetProps, value:this.value }
            }
        },
    }
</script>
