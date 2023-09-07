<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { TemplateRenderer } from '@/components';
    import { PanelWidgetData } from "@/types";
    import { Link } from '@inertiajs/vue3';

    defineProps<{
        widget: Omit<PanelWidgetData, 'value'>,
        value: PanelWidgetData['value'],
    }>();
</script>

<template>
    <div class="SharpWidgetPanel">
        <template v-if="widget.title">
            <h2 class="SharpWidget__title mb-2">
                {{ widget.title }}
            </h2>
        </template>
        <TemplateRenderer name="WidgetPanel" :template="widget.template" :template-data="value.data" />
        <template v-if="widget.link">
            <Link class="stretched-link" :href="widget.link">
                <span class="visually-hidden">
                    {{ __('sharp::dashboard.widget.link_label') }}
                </span>
            </Link>
        </template>
    </div>
</template>
