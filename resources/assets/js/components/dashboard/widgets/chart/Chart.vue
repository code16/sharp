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
    // Removed because Vue duplication
    import Bar from 'vue-chartjs/es/BaseCharts/Bar';
    import Line from 'vue-chartjs/es/BaseCharts/Line';
    import Pie from 'vue-chartjs/es/BaseCharts/Pie';
    import Chartjs from './Chartjs';
    import Legend from './Legend';

    const noop = ()=>{};

    export default {
        name:'SharpWidgetChart',

        components: {
            [Chartjs.name]:Chartjs,
            [Legend.name]:Legend
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
                    bar:Bar, line:Line, pie:Pie
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
                    legendCallback: noop
                }
            },
            data() {
                return {
                    ...this.value,
                    datasets: this.datasets
                }
            },
            datasets() {
                return this.value.datasets.map(dataset=>({
                    ...dataset,
                    ...this.datasetColor(dataset)
                }))
            }
        },
        methods: {
            datasetColor({ color }) {
                return this.display==='line'
                    ? { borderColor: color, fill: false }
                    : { backgroundColor: color };
            }
        },
        mounted() {

        }
    }
</script>