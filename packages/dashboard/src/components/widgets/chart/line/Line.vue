<template>
    <div>
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script>
    import { Line, mixins } from 'vue-chartjs';

    export default {
        extends: Line,
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
                        xAxes: [ {
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            }
        },
        mounted() {
            this.renderChart(this.chartData, this.options);
        },
    }
</script>