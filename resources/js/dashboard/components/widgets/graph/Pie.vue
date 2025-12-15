<script setup lang="ts">
    import { computed } from "vue";
    import { GraphWidgetData } from "@/types";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { VisSingleContainer, VisDonut, VisTooltip, VisBulletLegend } from "@unovis/vue";
    import { BulletLegendConfigInterface, CrosshairConfigInterface, DonutConfigInterface, Donut, Tooltip } from "@unovis/ts";

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const data = computed(() => props.value?.datasets?.map(dataset => ({
        name: dataset.label ?? '',
        color: dataset.color,
        value: dataset.data[0] ?? 0,
    })));
    type Datum = typeof data.value[number];
    type CallbackData = { data: Datum };
    const tooltipTemplate = (_, i: number) => {
        const item = props.value?.datasets?.[i];
        const total = props.value?.datasets?.reduce((total, d) => total + (d.data[0] ?? 0), 0);
        return `<div class="text-sm">${item.label}: ${item.data}
            <span class="opacity-50">(${new Intl.NumberFormat(undefined, { style: 'percent', maximumFractionDigits: 2}).format((item.data[0] ?? 0) / total)})</span></div>`;
    }
</script>

<template>
    <div class="min-h-[250px] sm:min-h-0 flex flex-col gap-y-2 gap-x-4 sm:flex-row sm:items-center sm:gap-4">
        <VisSingleContainer>
            <VisDonut
                v-bind="{
                    data: data,
                    value: d => d.value,
                    color: d => d.color,
                    arcWidth: 50,

                    attributes: {
                         [Donut.selectors.segment]: {
                             'class'() {
                                 return `${Donut.selectors.segment} relative fill-(--color) hover:stroke-10! stroke-(--color)! transition-all [transform-box:fill-box] origin-center`;
                             },
                             'style'(data: CallbackData) {
                                 return `--color:${data.data.color}`;
                                 // return { '--color': data.color }
                             }
                         }
                    }
                } as DonutConfigInterface<Datum>"
            />
            <VisTooltip :triggers="{ [Donut.selectors.segment]: tooltipTemplate }" />
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
