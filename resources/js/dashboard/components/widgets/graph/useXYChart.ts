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
    TrimMode,
    getNearest,
} from "@unovis/ts";
import { ChartConfig, ChartTooltipContent, componentToString } from "@/components/ui/chart";
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
        return props.widget.displayHorizontalAxisAsTimeline
            ? new Date(props.value.labels[i]).getTime()
            : i;
    };
    const y = computed((): XYComponentConfigInterface<Datum>['y'] => props.value?.datasets.map((dataset, i) => (d) => d[i]));
    const xScale = computed((): XYComponentConfigInterface<Datum>['xScale'] => {
        return props.widget.displayHorizontalAxisAsTimeline
            ? Scale.scaleUtc() as any
            : undefined
    });
    const color = computed((): ColorAccessor<Datum | Datum[]> => props.value?.datasets.map((dataset, i) => dataset.color));

    const chartConfig = computed((): ChartConfig =>
        Object.fromEntries(props.value?.datasets.map((dataset, i) => [i, ({ label: dataset.label, color: dataset.color })]))
    );

    const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, {
        labelFormatter: (x) => {
            if(props.widget.displayHorizontalAxisAsTimeline) {
                const nearestDate = new Date(
                    getNearest(
                        props.value.labels.map((label) => new Date(label).getTime()),
                        (x as Date).getTime(),
                        v => v
                    )
                );
                return formatDate(nearestDate);
            }
            return props.value.labels[Math.round(x as number)];
        }
    });

    const rotate = computed(() =>
        !props.widget.options.horizontal
        && !props.widget.enableHorizontalAxisLabelSampling
        && !props.widget.displayHorizontalAxisAsTimeline
        && props.value?.labels?.length >= 10
    );


    const xAxisConfig = computed((): AxisConfigInterface<Datum> => ({
        tickValues: (() => {
            if(props.widget.displayHorizontalAxisAsTimeline) {
                return props.value.labels.length < 10
                    ? props.value.labels.map((label) => new Date(label))
                    : undefined as any; // let unovis handle number of ticks
            }
            if(!props.widget.enableHorizontalAxisLabelSampling) {
                return props.value.labels.map((_, i) => i);
            }
        })(),
        tickFormat: (tick, i) => {
            if(props.widget.displayHorizontalAxisAsTimeline) {
                return formatDate(tick as Date);
            }
            return props.value?.labels?.[tick as number] ?? '';
        },
        tickTextTrimType: TrimMode.End,
        // tickTextAlign: rotate.value ? TextAlign.Left : props.widget.options.horizontal ? TextAlign.Right : TextAlign.Center,
        tickTextAlign: rotate.value ? TextAlign.Right : props.widget.options.horizontal ? TextAlign.Right : TextAlign.Center,
        tickTextFitMode: rotate.value ? FitMode.Wrap : FitMode.Trim,
        // tickTextAngle: rotate.value ? 45 : undefined,
        tickTextAngle: rotate.value ? -45 : undefined,
        tickTextWidth: rotate.value ? 100 : undefined,
    }));


    const needsDecimals = computed(() =>
        props.value?.datasets.every(dataset =>
            dataset.data.every(value => Math.abs(value) > 0 && Math.abs(value) < 1)
        )
    );
    const yAxisConfig = computed((): AxisConfigInterface<Datum> => ({
        tickFormat: (tick) => {
            if(!Number.isInteger(tick) && !needsDecimals.value) {
                return '';
            }
        }
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
        yAxisConfig,
    };
}


function utc(d: Date) {
    const d2 = new Date(d);
    d2.setMinutes(d2.getMinutes() + d2.getTimezoneOffset());
    return d2;
}

function formatDate(date: Date) {
    date = utc(date);
    const hasHour = date.getHours() !== 0 || date.getMinutes() !== 0;
    return new Intl.DateTimeFormat(undefined, {
        day: '2-digit',
        month: 'short',
        hour: hasHour ? '2-digit' : undefined,
        minute: hasHour ? '2-digit' : undefined,
    })
        .format(date);
}
