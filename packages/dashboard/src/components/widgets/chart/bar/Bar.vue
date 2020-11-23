<template>
    <div>
        <ApexChart
            type="bar"
            :series="chartData.series"
            :options="options"
            height="100%"
        />
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts';
    import { defaultChartOptions } from "../../../../util/chart";

    export default {
        components: {
            ApexChart,
        },
        props: {
            chartData: Object,
        },
        computed: {
            options() {
                return {
                    ...defaultChartOptions(),
                    legend: {
                        showForSingleSeries: true,
                        position: 'bottom',
                    },
                    xaxis: {
                        categories: this.chartData.labels,
                        // type: 'datetime',
                    },
                    colors: this.chartData.colors,
                    plotOptions: {
                        bar: {
                            columnWidth: '100%',
                        }
                    },
                }
            }
        },
    }
</script>