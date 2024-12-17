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

export function useApexCharts(
    props: DashboardWidgetProps<GraphWidgetData>,
    additionalOptions: ({ width }: { width: number }) => ApexOptions
) {
    const zoomed = ref(false);
    const apexChartsComponent = useTemplateRef<VueApexChartsComponent>('apexChartsComponent');
    const el = computed<HTMLElement>(() => apexChartsComponent.value?.$el);
    const width = ref(0);
    const redraw = debounce(() => {
        apexChartsComponent.value.chart?.updateOptions({}, true);
        width.value = el.value.clientWidth;
        el.value.style.overflow = 'visible';
    }, 100);
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
                events: {
                    zoomed: () => {
                        zoomed.value = true;
                    },
                },
                animations: {
                    enabled: false,
                },
                toolbar: {
                    show: zoomed.value,
                    tools: {
                        pan: false,
                        zoom: true,
                        download: false,
                    },
                },
                locales: [
                    en, fr, ru, es, de,
                ],
                defaultLocale: document.documentElement.lang,
                redrawOnParentResize: false,
                redrawOnWindowResize: false,
            },
            legend: {
                show: widget.showLegend && !widget.minimal,
                showForSingleSeries: true,
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
