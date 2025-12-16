<script setup lang="ts">
    import { computed } from 'vue'
    import { DashboardWidgetProps } from '@/dashboard/types'
    import { GraphWidgetData } from '@/types'
    import { normalizeColor } from '@/dashboard/utils/chart'
    import { useEcharts } from '@/dashboard/components/widgets/graph/useEcharts'

    import VChart from 'vue-echarts'
    import { EChartsOption } from "echarts";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>()

    const { echartsComponent, options } = useEcharts(props, () => {
        const { widget, value } = props

        const isMinimal = widget.minimal
        const curved = widget.options?.curved ?? true

        return {
            grid: isMinimal
                ? { left: 0, right: 0, top: 0, bottom: 0 }
                : { left: 0, right: 0, top: 0, bottom: 20 },

            legend: {
                show: !isMinimal,
                bottom: 0,
            },

            xAxis: {
                type: widget.dateLabels ? 'time' : 'category',
                data: widget.dateLabels ? undefined : value?.labels,
                show: !isMinimal,
                boundaryGap: false,
            },

            yAxis: {
                type: 'value',
                show: !isMinimal,
            },

            series:
                value?.datasets?.map(dataset => ({
                    type: 'line',
                    name: dataset.label,
                    data: dataset.data,
                    smooth: curved,
                    symbol: 'none',
                    lineStyle: {
                        width: 2,
                        color: normalizeColor(dataset.color),
                    },
                    itemStyle: {
                        color: normalizeColor(dataset.color),
                    },
                })) ?? [],
        }
    })
</script>

<template>
    <div>
        <VChart
            ref="echartsComponent"
            class="mt-2"
            :class="{ 'mb-2': options.legend?.show }"
            :option="options"
            autoresize
        />
    </div>

</template>
