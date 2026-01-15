import { DashboardWidgetProps } from "@/dashboard/types";
import { AreaGraphWidgetData, BarGraphWidgetData, GraphWidgetData, LineGraphWidgetData } from "@/types";
import { computed } from "vue";
import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";
import {
    AxisConfigInterface,
    ColorAccessor,
    FitMode,
    Scale,
    TextAlign,
    TrimMode,
    getNearest,
    XYContainerConfigInterface,
} from "@unovis/ts";
import { extent } from 'd3-array';
import { ChartConfig, ChartTooltipContent, componentToString } from "@/components/ui/chart";
import { useCurrentElement, useElementSize } from "@vueuse/core";
export type Datum = number[] & { total?: number };

export function useXYChart(props: DashboardWidgetProps<LineGraphWidgetData | BarGraphWidgetData | AreaGraphWidgetData>) {
    const data = computed((): Datum[] => props.value?.datasets?.reduce((res, dataset, i) => {
        dataset.data.forEach((v, j) => {
            res[j] ??= [];
            res[j][i] = v;
            if(props.widget.display === 'area' && props.widget.stacked) {
                res[j].total ??= 0;
                res[j].total += v;
            }
        });
        return res;
    }, []));
    const x: XYComponentConfigInterface<Datum>['x'] = (d, i) => {
        return props.widget.displayHorizontalAxisAsTimeline
            ? new Date(props.value.labels[i]).getTime()
            : i;
    };
    const y = computed((): XYComponentConfigInterface<Datum>['y'] => props.value?.datasets.map((dataset, i) => (d) => d[i]));
    const { width, height } = useElementSize(useCurrentElement());
    const yNumTicks = computed(() => {
        // taken from https://github.com/f5/unovis/blob/8b9eae7d9787c8414d5caa5970f13f67a02f6654/packages/ts/src/components/axis/index.ts#L407
        // approximate the height by remove 75px to the container (bottom labels)
        const yAxisHeight = height.value - 75;
        return height.value ? Math.pow(yAxisHeight, 0.85) / 25 : 5;
    })
    const yExtent = computed(() => extent(
        props.widget.display === 'area' && props.widget.stacked
            ? data.value.map(d => d.total)
            : data.value.flatMap(d => d)
    ));
    const yScale = computed(() => {
        return Scale.scaleLinear().domain(yExtent.value).nice(yNumTicks.value)
    });
    const containerConfig = computed((): XYContainerConfigInterface<Datum> => {
        return {
            xScale: props.widget.displayHorizontalAxisAsTimeline
                ? Scale.scaleUtc() as any
                : undefined,
            yScale: yScale.value,
            yDomain: yScale.value.domain() as [number, number],
        }
    });
    const color = computed((): ColorAccessor<Datum | Datum[]> => props.value?.datasets.map((dataset, i) => dataset.color));

    const chartConfig = computed((): ChartConfig => ({
        ...Object.fromEntries(props.value?.datasets.map((dataset, i) => [i, ({
            label: dataset.label,
            color: dataset.color,
        })])),
        ...props.widget.display === 'area' && props.widget.showStackTotal && {
            total: {
                label: props.widget.stackTotalLabel,
                tooltipOnly: true,
            },
        },
    }));

    const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, {
        labelFormatter: (x) => {
            if(props.widget.displayHorizontalAxisAsTimeline) {
                const nearestDate = new Date(
                    getNearest(
                        props.value.labels.map((label) => new Date(label).getTime()),
                        x as number, // timestamp
                        v => v
                    )
                );
                return formatDate(nearestDate);
            }
            return props.value.labels[Math.round(x as number)];
        }
    });

    const rotate = computed(() =>
        !('horizontal' in props.widget && props.widget.horizontal)
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
        tickTextAlign: rotate.value
            ? TextAlign.Right
            : ('horizontal' in props.widget && props.widget.horizontal)
                ? TextAlign.Right
                : TextAlign.Center,
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
        },
        tickValues: yScale.value.ticks(yNumTicks.value),
    }));

    return {
        data,
        x,
        y,
        color,
        tooltipTemplate,
        containerConfig,
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
