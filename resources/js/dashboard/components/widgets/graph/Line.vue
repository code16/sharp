<script setup lang="ts">
    import { useChartOptions } from "@/dashboard/components/widgets/graph/useChartOptions";
    import { computed } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { GraphWidgetData } from "@/types";
    import ApexChart from "vue3-apexcharts";

    const props = defineProps<{
        widget: Omit<GraphWidgetData, 'value'>,
        value: GraphWidgetData['value'],
    }>();

    const options = useChartOptions(
        computed(() => props.widget),
        computed(() => {
            const { widget, value } = props;
            return {
                chart: {
                    type: 'area',
                    sparkline: {
                        enabled: widget.minimal,
                    },
                },
                colors: value?.datasets?.map(dataset => normalizeColor(dataset.color)),
                dataLabels: {
                    enabled: false
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
                    width: 4,
                    curve: widget.options?.curved ?? true ? 'smooth' : 'straight',
                },
                xaxis: {
                    type: widget.dateLabels ? 'datetime' : 'category',
                },
            }
        })
    )
</script>

<template>
    <div class="mt-2" :class="{ 'mb-2': options.legend.show }">
        <ApexChart
            :options="options"
            :series="options.series"
            :height="options.chart.height"
        />
    </div>
</template>
