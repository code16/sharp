import { ApexOptions } from 'apexcharts';
import en from "apexcharts/dist/locales/en.json";
import fr from "apexcharts/dist/locales/fr.json";
import ru from "apexcharts/dist/locales/ru.json";
import es from "apexcharts/dist/locales/es.json";
import de from "apexcharts/dist/locales/de.json";
import merge from 'lodash/merge';
import { computed, MaybeRefOrGetter, ref, toValue, useTemplateRef } from "vue";
import { GraphWidgetData } from "@/types";
import { VueApexChartsComponent } from "vue3-apexcharts";
import { useResizeObserver } from "@vueuse/core";
import { DashboardWidgetProps } from "@/dashboard/types";
import debounce from "lodash/debounce";
import { useColorMode } from "@/composables/useColorMode";

export function useApexCharts(
    props: DashboardWidgetProps<GraphWidgetData>,
    additionalOptions: ({ width }: { width: number }) => ApexOptions
) {
    const apexChartsComponent = useTemplateRef<VueApexChartsComponent>('apexChartsComponent');
    const el = computed<HTMLElement>(() => apexChartsComponent.value?.$el);
    const width = ref(0);
    const redraw = debounce(() => {
        apexChartsComponent.value.chart?.updateOptions({}, true);
        width.value = el.value.clientWidth;
        el.value.style.overflow = 'visible';
    }, 100);
    const mode = useColorMode();
    useResizeObserver(apexChartsComponent, () => {
        el.value.style.overflow = 'hidden';
        if(!width.value) {
            width.value = el.value.clientWidth;
        }
        redraw();
    });
    const options = computed<ApexOptions>(() => {
        const widget = props.widget;
        const baseOptions: ApexOptions = {
            chart: {
                height: widget.height ?? '100%',
                width: '100%',
                parentHeightOffset: 0,
                animations: {
                    enabled: false,
                },
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
                selection: {
                    enabled: false,
                },
                locales: [
                    en, fr, ru, es, de,
                ],
                defaultLocale: document.documentElement.lang,
                redrawOnParentResize: false,
                redrawOnWindowResize: false,
                background: 'transparent',
            },
            legend: {
                show: widget.showLegend && !widget.minimal,
                showForSingleSeries: true,
            },
            theme: {
                mode: mode.value
            },
            tooltip: {
                y: {
                    title: {
                        // @ts-ignore
                        formatter: (seriesName, {seriesIndex}) =>
                            seriesName !== `series-${seriesIndex + 1}` ? `${seriesName}:` : ''
                    }
                }
            },
        };

        return merge(baseOptions, additionalOptions({ width: width.value }));
    });

    return {
        apexChartsComponent,
        options,
    };
}
