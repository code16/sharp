<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { useChartOptions } from "@/dashboard/components/widgets/graph/useChartOptions";
    import { computed } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import ApexChart from "vue3-apexcharts";

    const props = defineProps<{
        widget: Omit<GraphWidgetData, 'value'>,
        value: GraphWidgetData['value'],
    }>();

    const options = useChartOptions(
        computed(() => props.widget),
        computed(() => {
            const datasets = props.value?.datasets?.filter(dataset => dataset.data?.length > 0);
            return {
                chart: {
                    type: 'pie'
                },
                colors: datasets?.map(dataset => normalizeColor(dataset.color)),
                labels: datasets?.map(dataset => dataset.label ?? ''),
                legend: {
                    position: 'right'
                },
                series: datasets?.map(dataset => dataset.data[0]),
            }
        })
    );
</script>

<template>
    <div>
        <ApexChart
            :options="options"
            :series="options.series"
            :height="options.chart.height"
        />
    </div>
</template>
