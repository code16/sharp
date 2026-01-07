<script setup lang="ts">
    import { AreaGraphWidgetData, GraphWidgetData } from "@/types";
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

    const props = defineProps<DashboardWidgetProps<AreaGraphWidgetData>>();

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
            } satisfies XYContainerConfigInterface<Datum>"
            :data="data"
        >
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: 'x',
                        gridLine: false,
                        domainLine: false,
                        ...xAxisConfig,
                    } satisfies AxisConfigInterface<Datum>"
                />
                <VisAxis
                    v-bind="{
                        type: 'y',
                        domainLine: false,
                        ...yAxisConfig,
                    } satisfies AxisConfigInterface<Datum>"
                />
            </template>


            <template v-if="props.widget.stacked">
                <VisArea
                    v-bind="{
                        x: x,
                        y: y,
                        color: props.value?.datasets.map((dataset, i) => props.widget.gradient ? `url(#fill-${i})` : dataset.color),
                        opacity: props.widget.opacity,
                        curveType: props.widget.curved ? CurveType.MonotoneX : CurveType.Linear,
                    } satisfies AreaConfigInterface<Datum>"
                />
            </template>
            <template v-else>
                <VisLine
                    v-bind="{
                        x: x,
                        y: y,
                        color: color,
                        lineWidth: 1,
                        curveType: props.widget.curved ? CurveType.MonotoneX : CurveType.Linear,
                    } satisfies LineConfigInterface<Datum>"
                />
                <template v-for="(dataset, i) in props.value?.datasets">
                    <VisArea
                        v-bind="{
                            x: x,
                            y: (d) => d[i],
                            color: props.widget.gradient ? `url(#fill-${i})` : dataset.color,
                            opacity: props.widget.opacity,
                            curveType: props.widget.curved ? CurveType.MonotoneX : CurveType.Linear,
                        } satisfies AreaConfigInterface<Datum>"
                    />
                </template>
            </template>


            <ChartCrosshair
                v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    hideWhenFarFromPointer: false,
                } satisfies CrosshairConfigInterface<Datum>"
            />

            <ChartTooltip />
        </VisXYContainer>

        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
