<template>
    <div>
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script>
    import { Pie, mixins } from 'vue-chartjs';

    export default {
        extends: Pie,
        mixins: [mixins.reactiveProp],
        props: {
            chartData: Object,
        },
        computed: {
            options() {
                return {
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    cutoutPercentage: 0,
                }
            }
        },
        mounted() {
            this.renderChart(this.chartData, this.options);
        },
    }
</script>