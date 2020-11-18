<template>
    <div>
        <ApexChart
            type="pie"
            :series="chartData.series"
            :options="options"
            height="100%"
        />
    </div>
</template>

<script>
    import ApexChart from "vue-apexcharts";
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
                    colors: this.chartData.colors,
                    labels: this.chartData.labels,
                    grid: {
                        padding: {
                            bottom: -30
                        }
                    }
                }
            }
        },
    }
</script>