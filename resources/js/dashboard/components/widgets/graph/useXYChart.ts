import { DashboardWidgetProps } from "@/dashboard/types";
import { GraphWidgetData } from "@/types";
import { computed, reactive, toRefs } from "vue";
import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";
import {
    AxisConfigInterface,
    ColorAccessor,
    FitMode,
    Scale,
    TextAlign,
    TrimMode
} from "@unovis/ts";
import { timeTickInterval } from 'd3-time';
import { ChartConfig, ChartTooltip, ChartTooltipContent, componentToString } from "@/components/ui/chart";
export type Datum = number[];

export function useXYChart(props: DashboardWidgetProps<GraphWidgetData>) {
    const data = computed((): Datum[] => props.value?.datasets?.reduce((res, dataset, i) => {
        dataset.data.forEach((v, j) => {
            res[j] ??= [];
            res[j][i] = v;
        });
        return res;
    }, []));
    const timeScale = true;
    const x: XYComponentConfigInterface<Datum>['x'] = (d, i) => {
        return props.widget.dateLabels && !props.widget.showAllLabels && timeScale
            ? new Date(props.value.labels[i]).getTime()
            : i;
    };
    const y = computed((): XYComponentConfigInterface<Datum>['y'] => props.value?.datasets.map((dataset, i) => (d) => d[i]));
    const xScale = computed((): XYComponentConfigInterface<Datum>['xScale'] => {
        return props.widget.dateLabels && !props.widget.showAllLabels && timeScale
            ? Scale.scaleUtc().domain([new Date(props.value.labels[0]), new Date(props.value.labels.at(-1))]) as any
            : undefined
    });
    const color = computed((): ColorAccessor<Datum | Datum[]> => props.value?.datasets.map((dataset, i) => dataset.color));

    const chartConfig = computed((): ChartConfig =>
        Object.fromEntries(props.value?.datasets.map((dataset, i) => [i, ({ label: dataset.label, color: dataset.color })]))
    );

    const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, {
        labelFormatter: (x) => {
            return props.widget.dateLabels
                ? new Intl.DateTimeFormat(undefined, { day: '2-digit', month: 'short' })
                    .format(x instanceof Date ? x : new Date(props.value.labels[Math.round(x as number)]))
                : props.value.labels[Math.round(x as number)];
        }
    });

    const rotate = computed(() =>
        !props.widget.options.horizontal
        && props.widget.showAllLabels
        && props.value?.labels?.length >= 10
    );

    const xAxisConfig = computed((): AxisConfigInterface<Datum> => ({
        tickValues: (() => {
            if(props.widget.showAllLabels) {
                return props.value.labels.map((_, i) => i);
            }
            if(props.widget.dateLabels && timeScale) {
                return props.value.labels.length < 10
                    ? props.value.labels.map((l) => new Date(l))
                    : xScale.value.ticks(10) as any;
            }
        })(),
        tickFormat: (tick, i) => {
            if(props.widget.dateLabels) {
                return new Intl.DateTimeFormat(undefined, { day: '2-digit', month: 'short' })
                    .format(tick instanceof Date ? tick : new Date(props.value.labels[tick as number]));
            }
            return props.value?.labels?.[tick as number];
        },
        tickTextTrimType: TrimMode.End,
        // tickTextAlign: rotate.value ? TextAlign.Left : props.widget.options.horizontal ? TextAlign.Right : TextAlign.Center,
        tickTextAlign: rotate.value ? TextAlign.Right : props.widget.options.horizontal ? TextAlign.Right : TextAlign.Center,
        tickTextFitMode: rotate.value ? FitMode.Wrap : FitMode.Trim,
        // tickTextAngle: rotate.value ? 45 : undefined,
        tickTextAngle: rotate.value ? -45 : undefined,
        tickTextWidth: rotate.value ? 100 : undefined,
    }));

    return {
        data,
        x,
        y,
        color,
        tooltipTemplate,
        timeScale,
        xScale,
        chartConfig,
        xAxisConfig,
    };
}
