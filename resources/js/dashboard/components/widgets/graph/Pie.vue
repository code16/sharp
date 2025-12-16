<script setup lang="ts">
    import { computed } from 'vue'
    import { DashboardWidgetProps } from '@/dashboard/types'
    import { GraphWidgetData } from '@/types'
    import { useBreakpoints } from '@/composables/useBreakpoints'
    import { normalizeColor } from '@/dashboard/utils/chart'
    import { useEcharts } from '@/dashboard/components/widgets/graph/useEcharts'

    import VChart from 'vue-echarts'

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>()

    const breakpoints = useBreakpoints()

    const { echartsComponent, options } = useEcharts(props, ({ width }) => {
        const datasets =
            props.value?.datasets?.filter(d => d.data?.length > 0) ?? []

        const isSmall = breakpoints.sm
        const veryNarrow = width && width < 380

        return {
            series: [
                {
                    type: 'pie',
                    radius: veryNarrow && isSmall ? '65%' : '70%',
                    center: isSmall ? ['40%', '50%'] : ['50%', '50%'],
                    avoidLabelOverlap: true,

                    label: {
                        show: true,
                        formatter: '{d}%',
                        fontFamily: 'inherit',
                    },

                    labelLine: {
                        show: true,
                    },

                    itemStyle: {
                        borderWidth: 0,
                    },

                    data: datasets.map(dataset => ({
                        value: dataset.data[0],
                        name: dataset.label ?? '',
                        itemStyle: {
                            color: normalizeColor(dataset.color),
                        },
                    })),
                },
            ],

            legend: isSmall
                ? {
                    orient: 'vertical',
                    right: 0,
                    top: width && width < 400 ? '45%' : 'center',
                }
                : {
                    orient: 'horizontal',
                    bottom: 0,
                    left: 'center',
                },
        }
    })
</script>

<template>
    <div>
        <VChart
            ref="echartsComponent"
            class="min-h-[250px] sm:min-h-0"
            :option="options"
            autoresize
        />
    </div>

</template>
