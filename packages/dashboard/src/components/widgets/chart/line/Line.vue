<template>
    <div>
        <ApexChart
            type="area"
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
                        position: 'bottom',
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: this.chartData.labels,
                    },
                    colors: this.chartData.colors,
                }
            }
        },
    }
</script>