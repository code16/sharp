import { ApexOptions } from 'apexcharts';
import en from "apexcharts/dist/locales/en.json";
import fr from "apexcharts/dist/locales/fr.json";
import ru from "apexcharts/dist/locales/ru.json";
import es from "apexcharts/dist/locales/es.json";
import de from "apexcharts/dist/locales/de.json";
import merge from 'lodash/merge';
import { computed, ComputedRef, ref } from "vue";
import { GraphWidgetData } from "@/types";

export function useChartOptions(
    widgetRef: ComputedRef<GraphWidgetData>,
    additionalOptions: ComputedRef<ApexOptions>
): ComputedRef<ApexOptions> {

    const zoomed = ref(false);

    return computed(() => {
        const widget = widgetRef.value;
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

        return merge(baseOptions, additionalOptions.value);
    });
}
