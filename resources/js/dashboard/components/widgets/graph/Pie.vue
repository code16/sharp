<script setup lang="ts">
    import { computed } from "vue";
    import { GraphWidgetData } from "@/types";
    import { normalizeColor } from "@/dashboard/utils/chart";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { useBreakpoints } from "@/composables/useBreakpoints";
    import { VisSingleContainer, VisDonut, VisTooltip, VisBulletLegend } from "@unovis/vue";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const breakpoints = useBreakpoints();

    const datasets = computed(() => (props.value?.datasets ?? []).filter(d => (d.data?.length ?? 0) > 0));
    const items = computed(() => datasets.value.map(ds => ({
        name: ds.label ?? '',
        color: normalizeColor(ds.color),
        value: ds.data[0] ?? 0,
    })));
    const showLegend = computed(() => props.widget.showLegend && !props.widget.minimal);
    const height = computed(() => props.widget.height ?? '100%');
</script>

<template>
    <div>
        <div class="min-h-[250px] sm:min-h-0" :class="breakpoints.sm ? 'sm:flex sm:items-center sm:gap-4' : ''">
            <VisSingleContainer :style="{ height }">
                <VisDonut
                    :data="items"
                    :value="d => d.value"
                    :category="d => d.name"
                    :color="d => d.color"
                    :innerRadius="0"
                />
                <VisTooltip />
            </VisSingleContainer>

            <div v-if="showLegend" :class="breakpoints.sm ? 'sm:ml-4' : 'mt-2'">
                <VisBulletLegend :items="items.map(i => ({ name: i.name, color: i.color }))" />
            </div>
        </div>
    </div>
</template>
