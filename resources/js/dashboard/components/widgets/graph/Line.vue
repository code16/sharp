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

    const { data, x, y, color, tooltipTemplate, xScale, chartConfig, xAxisConfig } = useXYChart(props);

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
                xScale: xScale,
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
                <VisAxis v-bind="{
                    type: 'y',
                    domainLine: false,
                } as AxisConfigInterface<Datum>" />
            </template>

            <VisLine
                v-bind="{
                    x: x,
                    y: y,
                    color: color,
                    lineWidth: props.widget.options.filled ? 1 : 2,
                    curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                } as LineConfigInterface<Datum>"
            />

            <template v-if="props.widget.options.filled">
                <template v-for="(dataset, i) in props.value?.datasets.toReversed()">
                    <VisArea
                        v-bind="{
                            x: x,
                            y: (d) => d[i],
                            color: () => `url(#fill-${i})`,
                            opacity: 0.6,
                            curveType: props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear,
                        } as AreaConfigInterface<Datum>"
                    />
                </template>
            </template>

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
                        v-bind="{ size: 6, x: x, y: y,
                        // strokeColor: color,
                        color: color } as ScatterConfigInterface<Datum>"
                    />
                </template>
            </template>
        </VisXYContainer>
        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
