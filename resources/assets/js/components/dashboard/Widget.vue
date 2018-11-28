<template>
    <article class="SharpWidget SharpCard" :class="{'SharpWidget--chart':widgetType==='graph', 'SharpWidget--link':hasLink}">
        <component :is="hasLink ? 'a' : 'div'" :href="widgetProps.link" :class="{SharpWidget__link:hasLink}">
            <div class="SharpCard__overview">
                <div class="SharpCard__overview-about">
                    <component :is="widgetComp" v-bind="exposedProps"></component>
                </div>
            </div>
        </component>
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
            },
            hasLink() {
                return !!this.widgetProps.link;
            }
        },
    }
</script>
