<template>
    <article class="SharpWidget SharpCard" :class="{'SharpWidget--chart':widgetType==='graph', 'SharpWidget--list':widgetType==='list', 'SharpWidget--link':hasLink}">
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
    import { widgetByType } from './widgets/index';

    export default {
        name:'SharpWidget',

        props: {
            widgetType: String,
            widgetProps: Object,
            value: Object
        },

        computed: {
            widgetComp() {
                return widgetByType(this.widgetType);
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
