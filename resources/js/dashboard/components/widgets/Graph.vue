<script setup lang="ts">
    import { GraphWidgetData } from '@/types';
    import type { Component } from 'vue';
    import Bar from './graph/Bar.vue';
    import Line from './graph/Line.vue';
    import Pie from './graph/Pie.vue';
    import Area from './graph/Area.vue';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { DashboardWidgetProps } from '@/dashboard/types';

    const props = defineProps<DashboardWidgetProps<GraphWidgetData>>();

    const components: Record<GraphWidgetData['display'], Component> = {
        'bar': Bar,
        'line': Line,
        'pie': Pie,
        'area': Area,
    };
</script>

<template>
    <Card>
        <template v-if="widget.title">
            <CardHeader>
                <CardTitle class="text-base/none font-semibold tracking-tight">
                    {{ widget.title }}
                </CardTitle>
            </CardHeader>
        </template>
        <CardContent :class="widget.minimal ? 'p-0!' : ''">
            <component
                :is="components[widget.display]"
                v-bind="props"
                class="[&_svg]:rounded-b-[calc(.5rem-1px)] [&_svg]:overflow-visible"
                :class="[
                    widget.height ? 'h-(--height)' : 'aspect-(--ratio)',
                ]"
                :style="{
                    '--ratio':  widget.height ? null : `${widget.ratioX} / ${widget.ratioY}`,
                    '--height': widget.height ? `${widget.height}px` : null,
                }"
            />
        </CardContent>
    </Card>
</template>
