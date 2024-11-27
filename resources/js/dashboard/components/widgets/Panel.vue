<script setup lang="ts">
    import { OrderedListWidgetData, PanelWidgetData } from "@/types";
    import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import MaybeInertiaLink from "@/components/MaybeInertiaLink.vue";
    import Content from "@/components/Content.vue";
    import { DashboardWidgetProps } from "@/dashboard/types";

    const props = defineProps<DashboardWidgetProps<PanelWidgetData>>();
</script>

<template>
    <Card class="relative">
        <template v-if="widget.title">
            <CardHeader>
                <CardTitle class="text-base/none font-semibold tracking-tight">
                    <template v-if="widget.link">
                        <MaybeInertiaLink class="hover:underline" :href="widget.link">
                            <span class="absolute inset-0"></span>
                            {{ widget.title }}
                        </MaybeInertiaLink>
                    </template>
                    <template v-else>
                        {{ widget.title }}
                    </template>
                </CardTitle>
            </CardHeader>
        </template>
        <CardContent>
            <Content class="content-sm text-sm" :html="value.html" />
        </CardContent>
    </Card>
</template>
