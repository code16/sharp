<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisAxis, VisBulletLegend, VisCrosshair, VisGroupedBar, VisTooltip, VisXYContainer } from "@unovis/vue";
    import {
        AxisConfigInterface,
        BulletLegendConfigInterface,
        CrosshairConfigInterface,
        FitMode,
        GroupedBar,
        GroupedBarConfigInterface,
        Orientation,
        TextAlign,
        TrimMode,
        XYContainerConfigInterface,
    } from '@unovis/ts'
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";
    import { ChartContainer, ChartLegendContent, ChartTooltip, ChartCrosshair } from "@/components/ui/chart";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate, chartConfig, xAxisConfig, xScale } = useXYChart(props);
</script>

<template>
    <ChartContainer :config="chartConfig" class="flex flex-col" cursor>
        <VisXYContainer class="flex-1 min-h-0"
            v-bind="{
                xScale: xScale,
            } as XYContainerConfigInterface<Datum>"
            :data="data"
        >
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: props.widget.options?.horizontal ? 'y' : 'x',
                        gridLine: false,
                        domainLine: false,
                        ...xAxisConfig,
                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis
                    v-bind="{
                        type: props.widget.options?.horizontal ? 'x' : 'y',
                        domainLine: false,
                    } as AxisConfigInterface<Datum>"
                />
            </template>

            <template v-if="!props.widget.options?.horizontal">
                <ChartCrosshair
                    v-bind="{
                        color: color,
                        template: tooltipTemplate,
                        hideWhenFarFromPointer: false,
                    } as CrosshairConfigInterface<Datum>"
                />
            </template>

            <ChartTooltip :triggers="{ [GroupedBar.selectors.barGroup]: tooltipTemplate }" />

            <VisGroupedBar
                v-bind="{
                    x: x,
                    y: y,
                    orientation: props.widget.options?.horizontal ? Orientation.Horizontal : Orientation.Vertical,
                    color: color,
                    barMinHeight: 5,
                } as GroupedBarConfigInterface<Datum>"
            />
        </VisXYContainer>

        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
