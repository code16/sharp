<script setup lang="ts">
    import { computed } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisLine, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { CurveType } from "@unovis/ts";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const labels = computed(() => props.value?.labels ?? []);
    const datasets = computed(() => props.value?.datasets ?? []);
    const curved = computed(() => props.widget.options?.curved ?? true);
    const series = computed(() => datasets.value.map(ds => ({
        name: ds.label,
        color: normalizeColor(ds.color),
        data: labels.value.map((label, i) => ({
            x: props.widget.dateLabels ? new Date(label) : label,
            y: ds.data[i] ?? 0,
        }))
    })));
    const showLegend = computed(() => props.widget.showLegend && !props.widget.minimal);
    const height = computed(() => props.widget.height ?? '100%');
</script>

<template>
    <div class="mt-2" :class="{ 'mb-2': showLegend }">
        <VisXYContainer :style="{ height }">
            <template v-if="!props.widget.minimal">
                <VisAxis type="x" />
                <VisAxis type="y" />
            </template>

            <template v-for="s in series" :key="s.name">
                <VisLine
                    :data="s.data"
                    :x="d => d.x"
                    :y="d => d.y"
                    :color="() => s.color"
                    :curveType="curved ? CurveType.MonotoneX : CurveType.Linear"
                    :id="() => s.name"
                />
            </template>

            <VisTooltip />
            <VisBulletLegend v-if="showLegend" :items="series.map(s => ({ name: s.name, color: s.color }))" />
        </VisXYContainer>
    </div>
</template>
