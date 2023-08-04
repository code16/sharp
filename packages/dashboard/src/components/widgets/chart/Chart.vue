<script setup lang="ts">
    import { GraphWidgetData } from "@/types";

    defineProps<GraphWidgetData>();
</script>

<template>
    <div>
        <template v-if="title">
            <h2 class="SharpWidget__title mb-2 mt-3 px-3">
                {{ title }}
            </h2>
        </template>
        <component
            :is="chartComp"
            :chart-data="chartData"
            :options="chartOptions"
            class="SharpWidgetChart"
            :class="classes"
            :style="style"
        />
    </div>
</template>

<script>
    import { getChartByType, transformData } from './index';

    export default {
        // props: {
        //     display: String,
        //     title: String,
        //     value: Object,
        //
        //     ratioX: Number,
        //     ratioY: Number,
        //
        //     height: Number,
        //     minimal: Boolean,
        //     showLegend: {
        //         type: Boolean,
        //         default: true,
        //     },
        //     dateLabels: Boolean,
        //     options: Object,
        // },

        data() {
            return {
                zoomed: false,
            }
        },

        computed: {
            classes() {
                return [
                    `SharpWidgetChart--${this.display}`,
                    {
                        'SharpWidgetChart--aspect-ratio': !this.height,
                    }
                ]
            },
            style() {
                return {
                    '--ratio-x': this.ratioX,
                    '--ratio-y': this.ratioY,
                }
            },
            chartComp() {
                return getChartByType(this.display);
            },
            chartData() {
                return transformData(this.display, this.value);
            },
            /**
             * @returns {import('apexcharts').ApexOptions}
             */
            chartOptions() {
                return {
                    chart: {
                        toolbar: {
                            show: this.zoomed,
                        },
                        height: this.height ?? '100%',
                        width: '100%',
                        sparkline: {
                            enabled: this.minimal,
                        },
                        parentHeightOffset: 0,
                        events: {
                            zoomed: () => {
                                this.zoomed = true;
                            },
                        },
                    },
                    xaxis: {
                        type: !this.options?.horizontal && this.dateLabels
                            ? 'datetime'
                            : 'category',
                    },
                    // yaxis: {
                    //     tickAmount: Math.abs(maxY - minY),
                    //     min: minY,
                    //     max: maxY,
                    // },
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
        },
    }
</script>
