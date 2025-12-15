<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisAxis, VisBulletLegend, VisCrosshair, VisGroupedBar, VisStackedBar, VisTooltip, VisXYContainer } from "@unovis/vue";
    import {
        AxisConfigInterface,
        BulletLegendConfigInterface,
        CrosshairConfigInterface,
        FitMode, GroupedBar,
        GroupedBarConfigInterface,
        TextAlign,
        TrimMode, XYContainerConfigInterface,
    } from '@unovis/ts'
    import { Datum, useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";
    import { computed } from "vue";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y, color, tooltipTemplate } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number] || '';
    }

    const rotate = computed(() => !props.widget.options.horizontal && props.value?.labels?.length >= 10);
</script>

<template>
    <div class="flex flex-col gap-4">
        <VisXYContainer class="flex-1 min-h-0"
            v-bind="{ } as XYContainerConfigInterface<Datum>"
            :data="data"
        >
            <template v-if="!props.widget.minimal">
                <VisAxis
                    v-bind="{
                        type: props.widget.options?.horizontal ? 'x' : 'y',

                    } as AxisConfigInterface<Datum>"
                />
                <VisAxis
                    v-bind="{
                        type: props.widget.options?.horizontal ? 'y' : 'x',
                         gridLine: false,
                        // tickValues: props.value?.labels.map((_, i) => i),
                        numTicks: props.value?.labels.length - 1,
                        tickFormat: tickFormat,
                        tickTextTrimType: TrimMode.End,
                        tickTextAlign: rotate ? TextAlign.Left : props.widget.options.horizontal ? TextAlign.Right : TextAlign.Center,
                        tickTextFitMode: rotate ? FitMode.Wrap : FitMode.Trim,
                        tickTextAngle: rotate ? 45 : undefined,
                        tickTextWidth: rotate ? 100 : undefined,
                    // } as AxisConfigInterface<Datum>"
                />
            </template>

            <template v-if="!props.widget.options?.horizontal">
                <VisCrosshair
                    v-bind="{
                    color: color,
                    template: tooltipTemplate,
                    hideWhenFarFromPointer: false,
                } as CrosshairConfigInterface<Datum>"
                />
            </template>

            <VisTooltip :triggers="{ [GroupedBar.selectors.barGroup]: tooltipTemplate }" />

            <VisGroupedBar
                v-bind="{
                    x: x,
                    y: y,
                    orientation: props.widget.options?.horizontal ? 'horizontal' : 'vertical',
                    color: color,
                } as GroupedBarConfigInterface<Datum>"
            />
        </VisXYContainer>

        <template v-if="props.widget.showLegend && !props.widget.minimal">
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
