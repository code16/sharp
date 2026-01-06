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

    const svgDefs = computed(() => props.value?.datasets.map((dataset, i) => `
        <linearGradient id="fill-${i}" x1="0" y1="0" x2="0" y2="1">
            <stop
                offset="5%"
                stop-color="${dataset.color}"
                stop-opacity="0.8"
            />
            <stop
                offset="95%"
                stop-color="${dataset.color}"
                stop-opacity="0.1"
            />
        </linearGradient>
    `).join(''));
</script>

<template>
    <ChartContainer class="flex flex-col" :config="chartConfig" cursor>
        <VisXYContainer class="flex-1 min-h-0"
            v-bind="{
                ...containerConfig,
                svgDefs: svgDefs,
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
                    lineWidth: 1,
                    curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                    // stacked: true,
                } as LineConfigInterface<Datum>"
            />

            <template v-for="(dataset, i) in props.value?.datasets">
                <VisArea
                    v-bind="{
                        x: x,
                        // y: y,
                        y: (d) => d[i],
                        // color: props.value?.datasets.map((dataset, i) => props.widget.options.gradient ? `url(#fill-${i})` : dataset.color),
                        color: props.widget.options.gradient ? `url(#fill-${i})` : dataset.color,
                        opacity: props.widget.options.opacity,
                        curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                    } as AreaConfigInterface<Datum>"
                />
            </template>

            <ChartCrosshair
                v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    hideWhenFarFromPointer: false,
                } as CrosshairConfigInterface<Datum>"
            />

            <ChartTooltip />
        </VisXYContainer>

        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
