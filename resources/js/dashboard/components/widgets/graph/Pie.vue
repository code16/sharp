<script setup lang="ts">
    import { computed, useAttrs, useTemplateRef } from "vue";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisSingleContainer, VisDonut, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import {
        BulletLegendConfigInterface, CrosshairConfigInterface, DonutConfigInterface, Donut, Tooltip,
        SingleContainerConfigInterface, SingleContainer, TooltipConfigInterface
    } from "@unovis/ts";
    import {
        ChartConfig,
        ChartContainer, ChartLegendContent,
        ChartTooltip,
        ChartTooltipContent,
        componentToString
    } from "@/components/ui/chart";
    import { UseElementSize } from "@vueuse/components";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const data = computed(() => props.value?.datasets?.map((dataset, i) => ({
        name: dataset.label ?? '',
        color: dataset.color,
        value: dataset.data[0] ?? 0,
        [i]: dataset.data[0] ?? 0,
    })));
    type Datum = typeof data.value[number];
    const chartConfig = computed((): ChartConfig =>
        Object.fromEntries(props.value?.datasets.map((dataset, i) => [i, ({ label: dataset.label, color: dataset.color })]))
    );
    const tooltipTemplate = componentToString(chartConfig, ChartTooltipContent, { hideLabel: true });
</script>

<template>
    <ChartContainer class="min-h-[250px] sm:min-h-0 flex flex-col gap-y-2 gap-x-4 sm:gap-4" :config="chartConfig" ref="container">
<!--        <UseElementSize class="flex-1 min-h-0 flex flex-col" v-slot="{ width, height }">-->
            <VisSingleContainer class="flex-1 min-h-0">
                <VisDonut
                    v-bind="{
                        value: d => d.value,
                        color: d => d.color,
                        // arcWidth: width ? log(Math.round(Math.min(width, height) * 0), width, height) : 20,
                        // showBackground: false,
                        arcWidth: 0,
                    } satisfies DonutConfigInterface<Datum>"
                    :data="data"
                />
                <ChartTooltip v-bind="{
                    triggers: { [Donut.selectors.segment]: tooltipTemplate }
                } satisfies TooltipConfigInterface" />
            </VisSingleContainer>
<!--        </UseElementSize>-->

        <template v-if="props.widget.showLegend && !props.widget.minimal">
            <ChartLegendContent />
        </template>
    </ChartContainer>
</template>
