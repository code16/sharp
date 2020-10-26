<template>
    <div>
        <ApexChart
            type="line"
            :series="chartData.series"
            :options="options"
        />
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts';

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