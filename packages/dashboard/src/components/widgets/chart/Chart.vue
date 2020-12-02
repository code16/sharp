<template>
    <div>
        <template v-if="title">
            <h2 class="my-2">{{title}}</h2>
        </template>
        <div class="SharpWidgetChart" :class="classes" ref="chart">
            <div class="SharpWidgetChart__sizer" :style="sizerStyle"></div>
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

            height: Number,
            minimal: Boolean,
            showLegend: {
                type: Boolean,
                default: true,
            },
            dateValues: Boolean,

            options: Object,
        },

        data() {
            return {
                resizing: true,
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
                // apex chart options
                return {
                    chart: {
                        height: this.height ?? '100%',
                        sparkline: {
                            enabled: this.minimal,
                        },
                        parentHeightOffset: 0,
                        redrawOnParentResize: false,
                        events: {
                            updated: () => {
                                this.resizing = false;
                            }
                        },
                        redrawOnWindowResize: () => {
                            this.resizing = true;
                            return true;
                        },
                    },
                    xaxis: {
                        type: !this.options?.horizontal && this.dateValues ? 'datetime' : 'category',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !!this.options?.horizontal,
                        }
                    },
                    legend: {
                        show: this.showLegend && !this.minimal,
                    },
                    stroke: {
                        curve: this.options?.curved ?? true ? 'smooth' : 'straight',
                    },
                }
            },
            hasRatio() {
                return !this.height;
            },
            classes() {
                return [
                    `SharpWidgetChart--${this.display}`,
                    {
                        'SharpWidgetChart--aspect-ratio': this.hasRatio,
                        'SharpWidgetChart--resizing': this.resizing,
                    }
                ]
            },
            sizerStyle() {
                return {
                    'padding-bottom': `${this.ratioY / this.ratioX * 100}%`,
                }
            },
        },
        async mounted() {
            await this.$nextTick();
            this.resizing = false;
        }
    }
</script>