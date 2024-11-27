<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { useApexCharts } from "@/dashboard/components/widgets/graph/useApexCharts";
    import { computed, useTemplateRef } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import ApexChart from "vue3-apexcharts";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { useBreakpoints } from "@/composables/useBreakpoints";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const breakpoints = useBreakpoints();
    const { apexChartsComponent, options } = useApexCharts(props, ({ width }) => {
        const datasets = props.value?.datasets?.filter(dataset => dataset.data?.length > 0);
        return {
            chart: {
                type: 'pie',
            },
            grid: {
                padding: {
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: 'inherit'
                },
                dropShadow: {
                    enabled: false,
                },
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        offset: -10
                    },
                    offsetX: breakpoints.sm ? -10 : 0,
                    customScale: width && breakpoints.sm && width < 380 ? 1.1 : 1,
                },
            },
            stroke: {
                show: false,
            },
            colors: datasets?.map(dataset => normalizeColor(dataset.color)),
            labels: datasets?.map(dataset => dataset.label ?? ''),
            legend: breakpoints.sm ? {
                position: 'right',
                offsetY: width && width < 400 ? -20 : 0,
                offsetX: width && width < 400 ? -25 : 0,
            } : {
                position: 'bottom',
                offsetX: -20,
            },
            series: datasets?.map(dataset => dataset.data[0]),
        }
    });
</script>

<template>
    <div ref="el">
        <ApexChart
            class="min-h-[250px] sm:min-h-0"
            :options="options"
            :series="options.series"
            :height="options.chart.height"
            ref="apexChartsComponent"
        />
    </div>
</template>
