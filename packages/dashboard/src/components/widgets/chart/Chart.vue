<template>
    <div>
        <template v-if="title">
            <h2 class="my-2">{{title}}</h2>
        </template>
        <div :class="classes" :style="style">
            <component
                :is="chartComp"
                :chart-data="chartData"
                :options="chartOptions"
                class="SharpWidgetChart__inner"
            />
        </div>
    </div>
</template>

<script>
    import { getChartByType, transformData } from './index';
    import ChartLegend from './Legend';

    export default {
        name: 'SharpWidgetChart',

        components: {
            ChartLegend,
        },

        props: {
            display: String,
            title: String,
            value: Object,

            ratioX: Number,
            ratioY: Number,

            options: Object,
        },

        data() {
            return {
                resizing: false,
            }
        },

        computed: {
            chartComp() {
                return getChartByType(this.display);
            },
            chartData() {
                return transformData(this.display, this.value);
            },
            chartOptions() {
                const options = this.options ?? {};
                // apex chart options
                return {
                    chart: {
                        height: options.height ?? '100%',
                        sparkline: {
                            enabled: options.minimal ?? false,
                        },
                        events: {
                            updated:() => {
                                console.log('updated');
                                this.resizing = false;
                            }
                        },
                        redrawOnWindowResize:() => {
                            this.resizing = true;
                            console.log('redraw');
                            return true;
                        },
                    },
                    legend: {
                        show: options.showLegend ?? true,
                    }
                }
            },
            hasRatio() {
                return !this.options?.height;
            },
            classes() {
                return [
                    `SharpWidgetChart SharpWidgetChart--${this.display}`,
                    {
                        'SharpWidgetChart--ratio': this.hasRatio,
                        'SharpWidgetChart--resizing': this.resizing,
                    }
                ]
            },
            style() {
                return {
                    'padding-top': this.hasRatio
                        ? `${this.ratioY / this.ratioX * 100}%`
                        : null,
                }
            },
        },
        methods: {
            handleResize() {
                this.resizing = true;
            }
        },
        mounted() {
            // window.addEventListener('resize', this.handleResize);
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.handleResize);
        }
    }
</script>