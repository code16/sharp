<script setup lang="ts">
    import { PanelWidgetData } from "@/types";
    import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import MaybeInertiaLink from "@/components/MaybeInertiaLink.vue";
    import Content from "@/components/Content.vue";
    import { DashboardWidgetProps } from "@/dashboard/types";
    import { __ }from '@/utils/i18n'

    const props = defineProps<DashboardWidgetProps<PanelWidgetData>>();
</script>

<template>
    <Card class="relative" :class="widget.link ? 'transition-colors hover:bg-muted/50' : ''">
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
        <template v-else-if="widget.link">
            <CardHeader class="pb-0">
                <MaybeInertiaLink class="hover:underline" :href="widget.link">
                    <span class="absolute inset-0"></span>
                    <span class="sr-only">{{ __('sharp::dashboard.widget.link_label') }}</span>
                    {{ widget.title }}
                </MaybeInertiaLink>
            </CardHeader>
        </template>
        <CardContent>
            <Content class="content-sm text-sm" :html="value.html" />
        </CardContent>
    </Card>
</template>
