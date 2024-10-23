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
                    type: 'bar'
                },
                colors: value?.datasets?.map(dataset => normalizeColor(dataset.color)),
                grid: {
                    show: false,
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
    )
</script>

<template>
    <div :class="{ 'mb-2': options.legend.show }">
        <ApexChart
            :options="options"
            :series="options.series"
            :height="options.chart.height"
        />
    </div>
</template>
