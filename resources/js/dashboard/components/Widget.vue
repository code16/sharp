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
    <div class="SharpWidget card" :class="{
        'SharpWidget--chart': widget.type === 'graph',
        'SharpWidget--panel': widget.type === 'panel',
        'SharpWidget--link': 'link' in widget && widget.link,
    }">
        <div class="card-body">
            <component
                :is="components[widget.type]"
                :value="value"
                :widget="widget"
            />
        </div>
    </div>
</template>
