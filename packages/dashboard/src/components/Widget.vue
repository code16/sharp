<template>
    <div class="SharpWidget card" :class="classes" :href="widgetProps.link">
        <div class="card-body">
            <component
                :is="widgetComp"
                :value="value"
                v-bind="widgetProps"
            />
        </div>
    </div>
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
                    'SharpWidget--panel': this.widgetType === 'panel',
                    'SharpWidget--link': this.widgetProps.link,
                }
            },
            widgetComp() {
                return widgetByType(this.widgetType, this.widgetProps.display);
            },
        },
    }
</script>
