<script lang="ts" setup>
    import { computed } from 'vue';
    import type { Component } from 'vue';
    import { Card, CardContent } from "@/components/ui/card";
    import { Badge } from "@/components/ui/badge";
    import { sanitizeVueTemplate } from "@/utils/sanitize";

    const props = defineProps<{
        template: string,
        components?: Record<string, Component>,
    }>();

    const component = computed(() => ({
        components: {
            'sharp-card': Card,
            'sharp-card-content': CardContent,
            'sharp-badge': Badge,
            ...props.components,
        },
        template: `<div data-sharp-template>${sanitizeVueTemplate(props.template ?? '')}</div>`,
    }));
</script>

<template>
    <component :is="component">
        <slot />
    </component>
</template>

