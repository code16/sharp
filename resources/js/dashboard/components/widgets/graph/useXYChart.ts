import { DashboardWidgetProps } from "@/dashboard/types";
import { GraphWidgetData } from "@/types";
import { computed, reactive, toRefs } from "vue";
import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";
import { AxisConfigInterface, ColorAccessor, CrosshairConfigInterface, Scale } from "@unovis/ts";
import { timeTickInterval } from 'd3-time';
export type Datum = number[];

export function useXYChart(props: DashboardWidgetProps<GraphWidgetData>) {
    const data = computed((): Datum[] => props.value?.datasets?.reduce((res, dataset, i) => {
        dataset.data.forEach((v, j) => {
            res[j] ??= [];
            res[j][i] = v;
        });
        return res;
    }, []));
    // const timeScale = false;
    const timeScale = true;
    const x: XYComponentConfigInterface<Datum>['x'] = (d, i) => {
        return props.widget.dateLabels && timeScale ? new Date(props.value.labels[i]).getTime(): i;
    };
    const y = computed((): XYComponentConfigInterface<Datum>['y'] => props.value?.datasets.map((dataset, i) => (d) => d[i]));
    const xScale = computed((): XYComponentConfigInterface<Datum>['xScale'] => {
        return props.widget.dateLabels && timeScale
            ? Scale.scaleUtc().domain([new Date(props.value.labels[0]), new Date(props.value.labels.at(-1))]) as any
            : undefined
    });
    const xTickValues = computed((): AxisConfigInterface<Datum>['tickValues'] => {
        // return undefined;
        if(props.widget.dateLabels && timeScale) {
            return props.value.labels.length < 10
                ? props.value.labels.map((l) => new Date(l))
                : xScale.value.ticks(10) as any;
        }
    });
    const color = computed((): ColorAccessor<Datum | Datum[]> => props.value?.datasets.map((dataset, i) => dataset.color));

    const tooltipTemplate = (d: Datum, x: number) => {
        const formattedLabel = props.widget.dateLabels
            ? new Intl.DateTimeFormat(undefined, { day: '2-digit', month: 'short' }).format(timeScale ? x : new Date(props.value.labels[Math.round(x as number)]))
            : props.value.labels[Math.round(x as number)];
        return `<div class="mb-1 text-sm">${formattedLabel}</div>
        ${
            props.value?.datasets.map((dataset, i) =>
                `<div class="flex items-center gap-2 text-sm">
                    <span class="size-2 rounded-full bg-(--color)" style="--color: ${dataset.color}"></span>
                    ${dataset.label ? `<span class="text-sm">${dataset.label}:</span>` : ''}
                    ${d[i]}
                </div>`
            ).join('')
        }`;
    }

    return { data, x, y, color, tooltipTemplate, timeScale, xScale, xTickValues, };
}
