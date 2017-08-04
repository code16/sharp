<template>
    <article class="SharpWidget" :class="{'SharpCard':widgetType!=='graph'}" tabindex="0">
        <div class="SharpCard__overview">
            <div class="SharpCard__overview-about">
                <component :is="widgetComp" v-bind="exposedProps"></component>
            </div>
        </div>
        <footer class="SharpCard__footer">

        </footer>
    </article>
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
