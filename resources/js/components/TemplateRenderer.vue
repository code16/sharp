<script lang="ts">
    import { Card, CardContent } from '@/components/ui/card';
    import { Badge } from '@/components/ui/badge';

    export const components = {
        'sharp-card': Card,
        'sharp-card-content': CardContent,
        'sharp-badge': Badge,
    };
</script>
<script lang="ts" setup>
    import { computed } from 'vue';
    import type { Component } from 'vue';

    const props = defineProps<{
        template: string,
        components?: Record<string, Component>,
    }>();

    const component = computed(() => ({
        components: {
            ...components,
            ...props.components,
        },
        template: `<div data-sharp-template>${sanitizeForVue(props.template ?? '')}</div>`,
    }));

    function sanitizeForVue(template: string) {
        return template.replaceAll('{{', '&lcub;&lcub;').replaceAll('}}', '&rcub;&rcub;');
    }
</script>

<template>
    <component :is="component">
        <slot />
    </component>
</template>

