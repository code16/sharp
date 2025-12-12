<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisAxis, VisBulletLegend, VisCrosshair, VisGroupedBar, VisTooltip, VisXYContainer } from "@unovis/vue";
    import {
        AxisConfigInterface,
        BulletLegendConfigInterface, CrosshairConfigInterface,
        FitMode,
        GroupedBar,
        GroupedBarConfigInterface,
        TextAlign,
        TooltipConfigInterface,
        TrimMode,
    } from '@unovis/ts'
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";
    import { computed } from "vue";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number] || '';
    }

    const rotate = computed(() => props.value?.labels?.length >= 10);
</script>

<template>
    <div class="flex flex-col gap-4">
        <VisXYContainer class="flex-1 min-h-0" :data="data">
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: widget.options?.horizontal ? 'x' : 'y',
                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis
                    v-bind="{
                        type: widget.options?.horizontal ? 'y' : 'x',
                        // tickValues: props.value?.labels.map((_, i) => i),
                        numTicks: props.value?.labels.length - 1,
                        tickFormat: tickFormat,
                        tickTextTrimType: TrimMode.End,
                        tickTextAlign: rotate ? TextAlign.Left : TextAlign.Center,
                        tickTextFitMode: rotate ? FitMode.Wrap : FitMode.Trim,
                        tickTextAngle: rotate ? 45 : undefined,
                        tickTextWidth: rotate ? 100 : undefined,
                    } as AxisConfigInterface<Datum>"
                />
            </template>

            <VisCrosshair
                v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    // hideWhenFarFromPointer: false,
                } as CrosshairConfigInterface<Datum>"
            />

            <VisGroupedBar
                v-bind="{
                    x: x,
                    y: y,
                    orientation: widget.options?.horizontal ? 'horizontal' : 'vertical',
                    color: color,
                } as GroupedBarConfigInterface<Datum>"
            />
            <VisTooltip v-bind="{ triggers: { [GroupedBar.selectors.root]: tooltipTemplate } } as TooltipConfigInterface" />
        </VisXYContainer>

        <template v-if="widget.showLegend && !widget.minimal">
            <div class="flex justify-center">
                <VisBulletLegend
                    v-bind="{
                        items: props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color })),
                    } as BulletLegendConfigInterface"
                />
            </div>
        </template>
    </div>
</template>
