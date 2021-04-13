<template>
    <component :is="tag" class="SharpWidget card" :class="classes" :href="widgetProps.link">
        <div class="card-body">
            <component :is="widgetComp" v-bind="exposedProps"></component>
        </div>
    </component>
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
            classes() {
                return {
                    'SharpWidget--chart': this.widgetType === 'graph',
                    'SharpWidget--link': this.widgetProps.link,
                }
            },
            tag() {
                return this.widgetProps.link ? 'a' : 'div';
            },
            widgetComp() {
                return widgetByType(this.widgetType, this.widgetProps.display);
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
