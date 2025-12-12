<script setup lang="ts">
    import { computed } from "vue";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisSingleContainer, VisDonut, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { BulletLegendConfigInterface, DonutConfigInterface } from "@unovis/ts";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const data = computed(() => props.value?.datasets?.map(dataset => ({
        name: dataset.label ?? '',
        color: dataset.color,
        value: dataset.data[0] ?? 0,
    })));
</script>

<template>
    <div class="min-h-[250px] sm:min-h-0 flex flex-col gap-y-2 gap-x-4 sm:flex-row sm:items-center sm:gap-4">
        <VisSingleContainer>
            <VisDonut
                v-bind="{
                    data: data,
                    value: d => d.value,
                    color: d => d.color,
                } as DonutConfigInterface<typeof data[number]>"
            />
            <VisTooltip />
        </VisSingleContainer>

        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <VisBulletLegend
                v-bind="{
                    items: props.value.datasets?.map(dataset => ({ name: dataset.label, color: dataset.color })),
                } as BulletLegendConfigInterface"
            />
        </template>
    </div>
</template>
