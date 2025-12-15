<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisBulletLegend, VisCrosshair, VisScatter } from "@unovis/vue";
    import {
        AxisConfigInterface, BulletLegendConfigInterface,
        CrosshairConfigInterface,
        CurveType,
        LineConfigInterface, ScatterConfigInterface, XYContainerConfigInterface,
        Scale, TextAlign, FitMode, Scatter,
    } from "@unovis/ts";
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate, timeScale, xScale, xTickValues } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick, i) => {
        if(props.widget.dateLabels) {
            return new Intl.DateTimeFormat(undefined, { day: '2-digit', month: 'short' })
                .format(timeScale ? tick : new Date(props.value.labels[tick]));
            // return Scale.scaleTime().tickFormat()(tick);// new Intl.DateTimeFormat().format(new Date(props.value.labels[tick]));
        }
        return props.value?.labels?.[tick as number];
    }
    // const rotate = computed(() => props.value?.labels?.length >= 10);
</script>

<template>
    <div class="mt-2">
        <VisXYContainer v-bind="{
            xScale,
            components: props.value?.datasets.map(dataset => new Scatter({
                size: 5, x: x, y: y, color: color
            })),
        } as XYContainerConfigInterface<Datum>" :data="data">
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: 'x',
                        tickFormat: tickFormat,
                        tickValues: xTickValues,
                        // minMaxTicksOnly: true
                        // tickValues: props.value?.labels.length < 5 ? props.value?.labels.map((_, i) => x(null, i)) as number[] : undefined,
                        // numTicks: props.value?.labels.length < 10 ? props.value?.labels.length - 1 : undefined,
                        // tickTextAlign: rotate ? TextAlign.Left : TextAlign.Center,
                        // tickTextFitMode: rotate ? FitMode.Wrap : FitMode.Trim,
                        // tickTextAngle: rotate ? 45 : undefined,
                        // tickTextWidth: rotate ? 100 : undefined,
                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis v-bind="{ type: 'y' } as AxisConfigInterface<Datum>" />
            </template>

            <VisLine
                v-bind="{
                    x: x,
                    y: y,
                    color: color,
                    lineWidth: 1,
                    curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                } as LineConfigInterface<Datum>"
            />

            <VisCrosshair
                v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    hideWhenFarFromPointer: false,
                } as CrosshairConfigInterface<Datum>"
            />
            <VisTooltip />

            <template v-if="props.value?.labels.length < 40">
                <template v-for="dataset in props.value?.datasets">
                    <VisScatter
                        v-bind="{ size: 4, x: x, y: y, strokeColor: color, color: 'var(--background)' } as ScatterConfigInterface<Datum>"
                    />
                </template>
            </template>

        </VisXYContainer>
        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <div class="mt-4 flex justify-center">
                <VisBulletLegend
                    v-bind="{
                        items: props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color })),
                    } as BulletLegendConfigInterface"
                />
            </div>
        </template>
    </div>
</template>
