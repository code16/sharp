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
            chart: {
                type: 'bar'
            },
            colors: value?.datasets?.map(dataset => normalizeColor(dataset.color)),
            grid: {
                show: false,
                padding: {
                    left: 0,
                }
            },
            labels: value?.labels,
            legend: {
                position: 'bottom',
            },
            plotOptions: {
                bar: {
                    horizontal: !!widget.options?.horizontal,
                }
            },
            series: value?.datasets?.map(dataset => ({
                data: dataset.data,
                name: dataset.label,
            })),
            xaxis: {
                type: !widget.options?.horizontal && widget.dateLabels
                    ? 'datetime'
                    : 'category',
                axisBorder: {
                    show: false,
                }
            },
            yaxis: {
                axisBorder: {
                    show: false,
                }
            },
        }
    })
</script>

<template>
    <div :class="{ 'mb-2': options.legend.show }" ref="el">
        <ApexChart
            :options="options"
            :series="options.series"
            :height="options.chart.height"
            ref="apexChartsComponent"
        />
    </div>
</template>
