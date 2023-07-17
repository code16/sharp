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
                    {{ lang('dashboard.widget.link_label') }}
                </span>
            </a>
        </template>
    </div>
</template>

<script>
    import { TemplateRenderer } from 'sharp/components';
    import { lang } from "sharp";

    export default {
        name: 'SharpWidgetPanel',
        inheritAttrs: false,
        components: {
            TemplateRenderer
        },
        props: {
            value: Object,
            title: String,
            template: String,
            link: String,
        },
        methods: {
            lang,
        },
    }
</script>
