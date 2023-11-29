<script setup lang="ts">
    import { GraphWidgetData } from "@/types";
    import type { Component } from "vue";
    import Bar from "./graph/Bar.vue";
    import Line from "./graph/Line.vue";
    import Pie from "./graph/Pie.vue";

    defineProps<{
        widget: Omit<GraphWidgetData, 'value'>,
        value: GraphWidgetData['value'],
    }>();

    const components: Record<GraphWidgetData['display'], Component> = {
        'bar': Bar,
        'line': Line,
        'pie': Pie,
    };
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
            :value="value"
            :widget="widget"
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
