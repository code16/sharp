<template>
    <div>
        <ApexChart
            type="pie"
            :series="chartData.series"
            :options="chartOptions"
            :height="options.chart.height"
        />
    </div>
</template>

<script>
    import merge from 'lodash/merge';
    import ApexChart from "vue-apexcharts";
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
            chartOptions() {
                return merge({},
                    defaultChartOptions(),
                    {
                        colors: this.chartData.colors,
                        labels: this.chartData.labels,
                        legend: {
                            position: 'right'
                        }
                    },
                    this.options,
                )
            }
        },
    }
</script>
