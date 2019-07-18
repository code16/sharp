<template>
    <div>
        <h2 v-if="title">{{title}}</h2>
        <sharp-legend :datasets="value.datasets"></sharp-legend>
        <div :class="classes" :style="styles">
            <sharp-chartjs :comp="chartComp" :data="data" :options="options"
                           :styles="{}" cssClasses="SharpWidgetChart__inner">
            </sharp-chartjs>
        </div>
    </div>
</template>

<script>
    import Pie from 'vue-chartjs/es/BaseCharts/Pie';
    import SharpChartjs from './Chartjs';
    import SharpLegend from './Legend';

    const noop = ()=>{};

    export default {
        name:'SharpWidgetPie',

        components: {
            SharpChartjs,
            SharpLegend
        },

        props: {
            display:String,
            title: String,
            value: Object,

            ratioX:Number,
            ratioY:Number,
        },
        computed: {
            classes() {
                return `SharpWidgetChart SharpWidgetChart--${this.display}`;
            },
            styles() {
                return { paddingTop:`${this.ratioY/this.ratioX*100}%` }
            },
            chartComp() {
                const map = {
                    pie:Pie
                };
                return map[this.display];
            },
            options() {
                return {
                    title: {
                        display: false
                    },
                    legend: {
                        display: false
                    },
                    maintainAspectRatio:false,
                    legendCallback: noop,
                    cutoutPercentage: 50
                }
            },
            data() {
                return {
                    ...this.value,
                    datasets: this.datasets,
                    labels: this.labels,
                }
            },
            datasets() {
                return [{
                    "data": this.value.datasets.reduce((ac, dataset) => [...ac, dataset.data[0]], []),
                    "backgroundColor": this.value.datasets.reduce((ac, dataset) => [...ac, dataset.color], []),
                }];
            },
            labels() {
                return this.value.datasets.reduce((ac, dataset) => [...ac, dataset.label], []);
            }
        },
    }
</script>