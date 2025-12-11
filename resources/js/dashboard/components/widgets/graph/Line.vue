<script setup lang="ts">
    import { computed } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisBulletLegend, VisGroupedBar } from "@unovis/vue";
    import { AxisConfigInterface, CurveType } from "@unovis/ts";
    import { useXYChart } from "@/dashboard/components/widgets/graph/useXYChart";
    import { XYComponentConfigInterface } from "@unovis/ts/core/xy-component/config";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const { data, x, y } = useXYChart(props);

    const tickFormat: AxisConfigInterface<number[]>['tickFormat'] = (tick) => {
        return props.value?.labels?.[tick as number];
    }
</script>

<template>
    <div class="mt-2" :class="{ 'mb-2': props.widget.showLegend && !props.widget.minimal }">
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

            <VisTooltip />
        </VisXYContainer>
        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <VisBulletLegend :items="props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color }))" />
        </template>
    </div>
</template>
