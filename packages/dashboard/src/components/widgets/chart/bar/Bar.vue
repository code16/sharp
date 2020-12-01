<template>
    <div>
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
    import { defaultChartOptions } from "../../../../util/chart";
    import merge from 'lodash/merge';

    export default {
        components: {
            ApexChart,
        },
        props: {
            chartData: Object,
            options: Object,
        },
        data() {
            return {
            }
        },
        computed: {
            chartOptions() {
                return merge({},
                    defaultChartOptions(),
                    {
                        legend: {
                            position: 'bottom',
                        },
                        xaxis: {
                            categories: this.chartData.labels,
                        },
                        grid: {
                            padding: {
                                right: this.options.plotOptions?.bar?.horizontal ? 8 : 0,
                            }
                        },
                        colors: this.chartData.colors,
                    },
                    this.options
                );
            }
        },
    }
</script>