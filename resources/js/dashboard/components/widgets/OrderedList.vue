<script setup lang="ts">
    import { OrderedListWidgetData } from "@/types";
    import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
    import { Badge } from "@/components/ui/badge";
    import MaybeInertiaLink from "@/components/MaybeInertiaLink.vue";
    import { __ } from "@/utils/i18n";
    import { DashboardWidgetProps } from "@/dashboard/types";

    const props = defineProps<DashboardWidgetProps<OrderedListWidgetData>>();
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
        <CardContent>
            <div class="-my-2 divide-y">
                <template v-for="item in value.data">
                    <div class="group/item isolate relative flex items-center py-4 gap-x-4">
                        <template v-if="item.url">
                            <div class="absolute inset-0 -inset-x-2 -z-10 transition-colors group-hover/item:bg-muted/50"></div>
                        </template>
                        <div class="flex-1">
                            <div class="content content-sm text-sm" v-html="item.label"></div>
                            <template v-if="item.url">
                                <MaybeInertiaLink :href="item.url" :aria-label="__('sharp::dashboard.widget.link_label')">
                                    <span class="absolute inset-0"></span>
                                </MaybeInertiaLink>
                            </template>
                        </div>
                        <template v-if="item.count != null">
                            <Badge variant="secondary">
                                {{ item.count }}
                            </Badge>
                        </template>
                    </div>
                </template>
            </div>
        </CardContent>
    </Card>
</template>
