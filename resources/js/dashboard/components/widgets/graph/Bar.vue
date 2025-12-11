<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisStackedBar, VisGroupedBar, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { AxisConfigInterface, Scale, StackedBar } from '@unovis/ts'
    import { useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y } = useXYChart(props);
    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number] || '';
    }
</script>

<template>
    <div :class="{ 'mb-2': widget.showLegend && !widget.minimal }">
        <VisXYContainer >
<!--            :x-scale="props.widget.dateLabels ? Scale.scaleTime() : Scale.scaleLinear()" -->
            <template v-if="!props.widget.minimal">
                <VisAxis :type="widget.options?.horizontal ? 'x' : 'y'" />
                <VisAxis :type="widget.options?.horizontal ? 'y' : 'x'"
                    :tickValues="props.value?.labels.map((_, i) => i)"
                    :tickFormat="tickFormat"
                />
            </template>

            <VisGroupedBar
                :data="data"
                :x="x"
                :y="y"
                :orientation="widget.options?.horizontal ? 'horizontal' : 'vertical'"
                :color="(_, i) => props.value?.datasets[i].color"
            />

            <VisTooltip />
        </VisXYContainer>
        <template v-if="widget.showLegend && !widget.minimal">
            <VisBulletLegend :items="props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color }))" />
        </template>
    </div>
</template>
