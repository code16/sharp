<template>
    <div class="mt-2">
        <ApexChart
            class="apexchart"
            type="area"
            :series="chartData.series"
            :options="chartOptions"
            :height="options.chart.height"
            :style="style"
        />
    </div>
</template>

<script>
    import merge from 'lodash/merge';
    import ApexChart from 'vue-apexcharts';
    import { defaultChartOptions } from "../../../../util/chart";

    export default {
        components: {
            ApexChart,
        },
        props: {
            chartData: Object,
            options: Object,
        },
        computed: {
            style() {
                return {
                    // 'margin-bottom': !this.options.chart?.sparkline?.enabled ? '-15px' : null,
                }
            },
            chartOptions() {
                return merge({},
                    defaultChartOptions(),
                    {
                        legend: {
                            position: 'bottom',
                        },
                        stroke: {
                            width: 4,
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: this.chartData.colors,
                    },
                    this.options
                );
            }
        },
    }
</script>