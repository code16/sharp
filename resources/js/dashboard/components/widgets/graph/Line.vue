<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisBulletLegend, VisCrosshair, VisScatter } from "@unovis/vue";
    import {
        AxisConfigInterface, BulletLegendConfigInterface,
        CrosshairConfigInterface,
        CurveType,
        LineConfigInterface, ScatterConfigInterface, XYContainerConfigInterface,
    } from "@unovis/ts";
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number];
    }
</script>

<template>
    <div class="mt-2">
        <VisXYContainer v-bind="{} as XYContainerConfigInterface<Datum>" :data="data">
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: 'x',
                        tickFormat: tickFormat,
                        // numTicks: props.value?.labels.length / 2,
                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis v-bind="{ type: 'y' } as AxisConfigInterface<Datum>" />
            </template>

            <VisLine
                v-bind="{
                    x: x,
                    y: y,
                    color: color,
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

            <template v-for="dataset in props.value?.datasets">
                <VisScatter
                    v-bind="{ size: 5, x: x, y: y, color: color } as ScatterConfigInterface<Datum>"
                />
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
