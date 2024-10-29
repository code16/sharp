<script setup lang="ts">
    import { WidgetData } from "@/types";
    import type { Component } from "vue";
    import Figure from "./widgets/Figure.vue";
    import OrderedList from "./widgets/OrderedList.vue";
    import Panel from "./widgets/Panel.vue";
    import Graph from "./widgets/Graph.vue";

    defineProps<{
        widget: WidgetData,
        value: WidgetData['value'],
    }>();

    const components: Record<WidgetData['type'], Component> = {
        'figure': Figure,
        'graph': Graph,
        'list': OrderedList,
        'panel': Panel,
    };
</script>

<template>
    <component
        :is="components[widget.type]"
        :value="value"
        :widget="widget"
    />
</template>
