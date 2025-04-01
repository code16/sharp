<script setup lang="ts">
    import { useApexCharts } from "@/dashboard/components/widgets/graph/useApexCharts";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { GraphWidgetData } from "@/types";
    import ApexChart from "vue3-apexcharts";
    import { DashboardWidgetProps } from "@/dashboard/types";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { apexChartsComponent, options } = useApexCharts(props, () => {
        const { widget, value } = props;
        return {
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 0,
                    right: 0,
                },
            },
            chart: {
                type: 'line',
                sparkline: {
                    enabled: widget.minimal,
                },
            },
            colors: value?.datasets?.map(dataset => normalizeColor(dataset.color)),
            dataLabels: {
                enabled: false,
            },
            labels: value?.labels,
            legend: {
                position: 'bottom',
            },
            series: value?.datasets?.map(dataset => ({
                data: dataset.data,
                name: dataset.label,
            })),
            stroke: {
                width: 2,
                curve: widget.options?.curved ?? true ? 'smooth' : 'straight',
            },
            xaxis: {
                type: widget.dateLabels ? 'datetime' : 'category',
            },
            yaxis: {
                show: !widget.minimal,
                labels: {
                    offsetX: -10,
                }
            }
        }
    })
</script>

<template>
    <div class="mt-2" :class="{ 'mb-2': options.legend.show }" ref="el">
        <ApexChart
            :options="options"
            :series="options.series"
            :height="options.chart.height"
            ref="apexChartsComponent"
        />
    </div>
</template>
