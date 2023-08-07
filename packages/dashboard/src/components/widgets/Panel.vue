<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { TemplateRenderer } from '@/components';
    import { PanelWidgetData } from "@/types";

    defineProps<PanelWidgetData>();
</script>

<template>
    <div class="SharpWidgetPanel">
        <template v-if="title">
            <h2 class="SharpWidget__title mb-2">
                {{ title }}
            </h2>
        </template>
        <TemplateRenderer name="WidgetPanel" :template="template" :template-data="value.data" />
        <template v-if="link">
            <a class="stretched-link" :href="link">
                <span class="visually-hidden">
                    {{ __('sharp::dashboard.widget.link_label') }}
                </span>
            </a>
        </template>
    </div>
</template>
