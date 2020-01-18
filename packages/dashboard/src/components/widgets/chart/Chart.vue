<template>
    <div>
        <template v-if="title">
            <h2 class="mb-2">{{title}}</h2>
        </template>
        <SharpChartLegend :datasets="value.datasets" />
        <div :class="classes" :style="style">
            <component :is="chartComp" :chart-data="chartData" class="SharpWidgetChart__inner" />
        </div>
    </div>
</template>

<script>
    import { getChartByType, transformData } from './index';
    import SharpChartLegend from './Legend';

    export default {
        name: 'SharpWidgetChart',

        components: {
            SharpChartLegend,
        },

        props: {
            display: String,
            title: String,
            value: Object,

            ratioX: Number,
            ratioY: Number,
        },

        computed: {
            chartComp() {
                return getChartByType(this.display);
            },
            chartData() {
                return transformData(this.display, this.value);
            },
            classes() {
                return `SharpWidgetChart SharpWidgetChart--${this.display}`;
            },
            style() {
                return {
                    'padding-top': `${this.ratioY/this.ratioX*100}%`,
                }
            },
        },
    }
</script>