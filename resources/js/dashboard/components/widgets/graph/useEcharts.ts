import { computed, ref, MaybeRefOrGetter, toValue, useTemplateRef } from 'vue'
import { useResizeObserver } from '@vueuse/core'
import debounce from 'lodash/debounce'
import merge from 'lodash/merge'
import { DashboardWidgetProps } from '@/dashboard/types'
import { GraphWidgetData } from '@/types'
import { useColorMode } from '@/composables/useColorMode'

import type { EChartsOption } from "echarts";
// ECharts locales
// @ts-ignore
import en from 'echarts/lib/i18n/langEN'
// @ts-ignore
import fr from 'echarts/lib/i18n/langFR'
// @ts-ignore
import ru from 'echarts/lib/i18n/langRU'
// @ts-ignore
import es from 'echarts/lib/i18n/langES'
// @ts-ignore
import de from 'echarts/lib/i18n/langDE'
import { registerLocale, use } from "echarts/core";
import { BarChart, LineChart, PieChart } from "echarts/charts";
import {
    GridComponent,
    TooltipComponent,
    LegendComponent,
} from 'echarts/components'
import { SVGRenderer } from "echarts/renderers";
import VChart from "vue-echarts";

use([
    SVGRenderer,
    BarChart,
    LineChart,
    PieChart,
    GridComponent,
    TooltipComponent,
    LegendComponent,
])
registerLocale('en', en);
registerLocale('fr', fr);
registerLocale('ru', ru);
registerLocale('es', es);
registerLocale('de', de);

export function useEcharts(
    props: DashboardWidgetProps<GraphWidgetData>,
    additionalOptions: ({ width }: { width: number }) => EChartsOption
) {
    const chartRef = useTemplateRef<InstanceType<typeof VChart>>('echartsComponent')
    const containerEl = computed<HTMLElement | undefined>(() => chartRef.value?.$el)

    const width = ref(0)
    const mode = useColorMode()

    // const resize = debounce(() => {
    //     chartRef.value?.resize()
    //     if (containerEl.value) {
    //         width.value = containerEl.value.clientWidth
    //         containerEl.value.style.overflow = 'visible'
    //     }
    // }, 100)

    useResizeObserver(chartRef, () => {
        if (!containerEl.value) return
        // containerEl.value.style.overflow = 'hidden'

        if (!width.value) {
            width.value = containerEl.value.clientWidth
        }

        // resize()
    })

    const options = computed<EChartsOption>(() => {
        const widget = props.widget
        const lang = document.documentElement.lang || 'en'

        const baseOptions: EChartsOption = {
            backgroundColor: 'transparent',

            animation: false,

            locale: lang,

            legend: {
                show: widget.showLegend && !widget.minimal,
            },

            tooltip: {
                trigger: 'axis',
                confine: true,
                formatter(params: any) {
                    if (!Array.isArray(params)) return ''
                    return params
                        .map(p =>
                            p.seriesName && !p.seriesName.startsWith('series-')
                                ? `${p.marker} ${p.seriesName}: ${p.value}`
                                : `${p.marker} ${p.value}`
                        )
                        .join('<br/>')
                },
            },

            grid: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0,
                containLabel: true,
            },

            textStyle: {
                color: mode.value === 'dark' ? '#e5e7eb' : '#111827',
            },
        }

        return merge(baseOptions, additionalOptions({ width: width.value }))
    })

    return {
        echartsComponent: chartRef,
        options,
    }
}
