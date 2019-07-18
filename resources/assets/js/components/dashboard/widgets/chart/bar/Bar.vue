<template>
    <div>
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script>
    import { Bar, mixins } from 'vue-chartjs';

    export default {
        extends: Bar,
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

                    scales: {
                        yAxes: [{
                            ticks: {
                                suggestedMin: 0,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            },
                            categoryPercentage: 0.5,
                            barPercentage: 0.2
                        }]
                    },
                }
            }
        },
        mounted() {
            this.renderChart(this.chartData, this.options);
        },
    }
</script>