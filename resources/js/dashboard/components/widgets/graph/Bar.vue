<script setup lang="ts">
    import { computed } from "vue";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisXYContainer, VisAxis, VisStackedBar, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { Scale, StackedBar } from '@unovis/ts'

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    // const data = computed(() => props.value?.datasets?.map(ds => ({})))

    const series = computed(() => props.value?.datasets?.map(ds => ({
        name: ds.label,
        color: normalizeColor(ds.color),
        data: props.value?.labels?.map((label, i) => ({
            x: props.widget.dateLabels ? new Date(label) : label,
            i,
            y: ds.data[i] ?? 0,
        }))
    })));
</script>

<template>
    <div :class="{ 'mb-2': widget.showLegend && !widget.minimal }">
        <VisXYContainer>
            <template v-if="!props.widget.minimal">
                <VisAxis type="x" />
                <VisAxis type="y" />
            </template>

<!--                <VisStackedBar-->
<!--                    :data="s.data"-->
<!--                    :x="(d, i) => i"-->
<!--                    :y="d => d.y"-->
<!--                    :orientation="widget.options?.horizontal ? 'horizontal' : 'vertical'"-->
<!--                    :color="() => s.color"-->
<!--                    :id="() => s.name"-->
<!--                />-->

            <VisTooltip />
            <template v-if="widget.showLegend && !widget.minimal">
                <VisBulletLegend
                    :items="series.map(s => ({ name: s.name, color: s.color }))"
                />
            </template>
        </VisXYContainer>
    </div>
</template>
