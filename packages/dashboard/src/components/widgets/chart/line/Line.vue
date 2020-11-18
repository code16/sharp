<template>
    <div>
        <ApexChart
            type="line"
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
                    stroke: {
                        curve: 'smooth'
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