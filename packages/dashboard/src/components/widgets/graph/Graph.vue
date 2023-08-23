<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import type { Component } from "vue";
    import Bar from "./bar/Bar.vue";
    import Line from "./line/Line.vue";
    import Pie from "./pie/Pie.vue";
    import { transformData } from "./index";
    import { computed, ref } from "vue";
    import { ApexOptions } from "apexcharts";

    const props = defineProps<{
        widget: Omit<GraphWidgetData, 'value'>,
        value: GraphWidgetData['value'],
    }>();

    const components: Record<GraphWidgetData['display'], Component> = {
        'bar': Bar,
        'line': Line,
        'pie': Pie,
    };

    const zoomed = ref(false);

    const chartOptions = computed<ApexOptions>(() => {
        const { widget } = props;
        return {
            chart: {
                toolbar: {
                    show: zoomed.value,
                },
                height: widget.height ?? '100%',
                width: '100%',
                sparkline: {
                    enabled: widget.minimal,
                },
                parentHeightOffset: 0,
                events: {
                    zoomed: () => {
                        zoomed.value = true;
                    },
                },
            },
            xaxis: {
                type: !widget.options?.horizontal && widget.dateLabels
                    ? 'datetime'
                    : 'category',
            },
            plotOptions: {
                bar: {
                    horizontal: !!widget.options?.horizontal,
                }
            },
            legend: {
                show: widget.showLegend && !widget.minimal,
            },
            stroke: {
                curve: widget.options?.curved ?? true ? 'smooth' : 'straight',
            },
        }
    });
</script>

<template>
    <div>
        <template v-if="widget.title">
            <h2 class="SharpWidget__title mb-2 mt-3 px-3">
                {{ widget.title }}
            </h2>
        </template>
        <component
            :is="components[widget.display]"
            :chart-data="transformData(widget.display, value)"
            :options="chartOptions"
            class="SharpWidgetChart"
            :class="[
                `SharpWidgetChart--${widget.display}`,
                {
                    'SharpWidgetChart--aspect-ratio': !widget.height,
                }
            ]"
            :style="{
                '--ratio-x': widget.ratioX,
                '--ratio-y': widget.ratioY,
            }"
        />
    </div>
</template>
