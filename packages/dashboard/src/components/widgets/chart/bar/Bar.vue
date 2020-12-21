<template>
    <div :class="{ 'mb-2': hasLegends }">
        <ApexChart
            class="apexchart"
            type="bar"
            :series="chartData.series"
            :options="chartOptions"
            :height="options.chart.height"
        />
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts';
    import { defaultChartOptions, hasLegends } from "../../../../util/chart";
    import merge from 'lodash/merge';

    export default {
        components: {
            ApexChart,
        },
        props: {
            chartData: Object,
            options: Object,
        },


        computed: {
            hasLegends() {
                return hasLegends(this.chartOptions);
            },
            chartOptions() {
                return merge({},
                    defaultChartOptions(),
                    {
                        legend: {
                            position: 'bottom',
                        },
                        labels: this.chartData.labels,
                        colors: this.chartData.colors,
                        grid: {
                            padding: {
                                right: 12,
                            }
                        },
                    },
                    this.options
                );
            }
        },
    }
</script>