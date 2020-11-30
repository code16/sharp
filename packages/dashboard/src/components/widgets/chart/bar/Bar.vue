<template>
    <div>
        <ApexChart
            type="bar"
            :series="chartData.series"
            :options="chartOptions"
            :height="options.chart.height"
            ref="chart"
        />
    </div>
</template>

<script>
    import ApexChart from 'vue-apexcharts';
    import { defaultChartOptions } from "../../../../util/chart";
    import merge from 'lodash/merge';

    export default {
        components: {
            ApexChart,
        },
        props: {
            chartData: Object,
            options: Object,
        },
        data() {
            return {
                // options: {
                //     dataLabels: {
                //         enabled: true,
                //     }
                // },
            }
        },
        computed: {
            chartOptions() {
                return merge({},
                    defaultChartOptions(),
                    {
                        legend: {
                            showForSingleSeries: true,
                            position: 'bottom',
                        },
                        xaxis: {
                            categories: this.chartData.labels,
                            // type: 'datetime',
                        },
                        colors: this.chartData.colors,
                        // stroke: {
                        //     show: true,
                        //     width: 1,
                        //     colors: ['transparent']
                        // },
                        plotOptions: {
                            bar: {
                                // columnWidth: '90%',
                            }
                        },
                        chart: {
                            events: {
                                mounted: this.handleChartRendered,
                            }
                        }
                    },
                    this.options
                );
            }
        },
        methods: {
            handleChartRendered(context) {
                // const barWidth = parseFloat(context.el.querySelector('[barWidth]').getAttribute('barWidth'));
                // this.options.dataLabels.enabled = barWidth > 20;
            },
        },
    }
</script>