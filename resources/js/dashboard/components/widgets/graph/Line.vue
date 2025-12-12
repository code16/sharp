<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisBulletLegend, VisCrosshair } from "@unovis/vue";
    import { AxisConfigInterface, CurveType } from "@unovis/ts";
    import { useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number];
    }
</script>

<template>
    <div class="mt-2">
        <VisXYContainer :data="data">
            <template v-if="!props.widget.minimal">
                <VisAxis type="x" :tickFormat="tickFormat" />
                <VisAxis type="y" />
            </template>

            <VisLine
                :x="x"
                :y="y"
                :color="(_, i) => props.value?.datasets[i].color"
                :curveType="props.widget.options.curved ? CurveType.MonotoneX : CurveType.Linear"
            />

            <VisCrosshair :color="(_, i) => props.value?.datasets[i].color" />

            <VisTooltip />
        </VisXYContainer>
        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <div class="mt-4 flex justify-center">
                <VisBulletLegend :items="props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color }))" />
            </div>
        </template>
    </div>
</template>
