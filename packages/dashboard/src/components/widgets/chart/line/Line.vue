<template>
    <div class="mt-2" :class="{ 'mb-2': hasLegends }">
        <ApexChart
            class="apexchart"
            type="area"
            :series="chartData.series"
            :options="chartOptions"
            :height="options.chart.height"
        />
    </div>
</template>

<script>
    import merge from 'lodash/merge';
    import ApexChart from 'vue-apexcharts';
    import { defaultChartOptions, hasLegends } from "../../../../util/chart";

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
                        colors: this.chartData.colors,
                        labels: this.chartData.labels,
                        legend: {
                            position: 'bottom',
                        },
                        stroke: {
                            width: 4,
                        },
                        dataLabels: {
                            enabled: false
                        },
                    },
                    this.options
                );
            }
        },
    }
</script>