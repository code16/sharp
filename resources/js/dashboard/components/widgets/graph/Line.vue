<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisCrosshair, VisScatter, VisArea } from "@unovis/vue";
    import {
        AxisConfigInterface, BulletLegendConfigInterface,
        CrosshairConfigInterface,
        CurveType,
        LineConfigInterface, ScatterConfigInterface, XYContainerConfigInterface,
        Scale, TextAlign, FitMode, Scatter, AreaConfigInterface,
    } from "@unovis/ts";
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";
    import { ChartContainer, ChartLegendContent, ChartTooltip, ChartCrosshair } from "@/components/ui/chart";
    import { computed } from "vue";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate, containerConfig, chartConfig, xAxisConfig, yAxisConfig } = useXYChart(props);
</script>

<template>
    <ChartContainer class="flex flex-col" :config="chartConfig" cursor>
        <VisXYContainer class="flex-1 min-h-0"
            v-bind="{
                ...containerConfig,
            } as XYContainerConfigInterface<Datum>"
            :data="data"
        >
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: 'x',
                        gridLine: false,
                        domainLine: false,
                        ...xAxisConfig,
                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis
                    v-bind="{
                        type: 'y',
                        domainLine: false,
                        ...yAxisConfig,
                    } as AxisConfigInterface<Datum>"
                />
            </template>

            <VisLine
                v-bind="{
                    x: x,
                    y: y,
                    color: color,
                    lineWidth: 2,
                    curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                } as LineConfigInterface<Datum>"
            />

            <ChartCrosshair
                v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    hideWhenFarFromPointer: false,
                } as CrosshairConfigInterface<Datum>"
            />

            <ChartTooltip />

            <template v-if="props.widget.options.showDots">
                <template v-for="dataset in props.value?.datasets">
                    <VisScatter
                        v-bind="{ size: 6, x: x, y: y, color: color } as ScatterConfigInterface<Datum>"
                    />
                </template>
            </template>
        </VisXYContainer>
        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
