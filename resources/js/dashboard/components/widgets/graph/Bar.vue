<script setup lang="ts">
    import { computed } from 'vue'
    import { DashboardWidgetProps } from '@/dashboard/types'
    import { GraphWidgetData } from '@/types'
    import { normalizeColor } from '@/dashboard/utils/chart'
    import { useEcharts } from '@/dashboard/components/widgets/graph/useEcharts'

    import VChart from 'vue-echarts'

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>()

    const { echartsComponent, options } = useEcharts(props, () => {
        const { widget, value } = props
        const horizontal = !!widget.options?.horizontal
        const dateLabels = widget.dateLabels

        return {
            grid: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 20,
                containLabel: true,
            },

            legend: {
                show: true,
                bottom: 0,
            },

            xAxis: horizontal
                ? {
                    type: 'value',
                    axisLine: { show: false },
                }
                : {
                    type: !horizontal && dateLabels ? 'time' : 'category',
                    data: !horizontal && !dateLabels ? value?.labels : undefined,
                    axisLine: { show: false },
                },

            yAxis: horizontal
                ? {
                    type: !dateLabels ? 'category' : 'time',
                    data: !dateLabels ? value?.labels : undefined,
                    axisLine: { show: false },
                }
                : {
                    type: 'value',
                    axisLine: { show: false },
                },

            series:
                value?.datasets?.map(dataset => ({
                    type: 'bar',
                    name: dataset.label,
                    data: dataset.data,
                    itemStyle: {
                        color: normalizeColor(dataset.color),
                    },
                    barMaxWidth: 32,
                })) ?? [],
        }
    })
</script>

<template>
    <div>
        <VChart
            ref="echartsComponent"
            :class="{ 'mb-2': options.legend?.show }"
            :option="options"
            autoresize
        />
    </div>

</template>
