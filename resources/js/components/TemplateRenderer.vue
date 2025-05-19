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

    const props = defineProps<{
        templateData?: Record<string, any>,
        templateProps?: string[],
        template: string,
    }>();

    const component = computed(() => ({
        components,
        template: `<div class="SharpTemplate">${props.template ?? ''}</div>`,
        props: [
            ...(props.templateProps || []),
            ...Object.keys(props.templateData ?? {}),
        ],
    }));
</script>

<template>
    <component :is="component" v-bind="templateData ?? {}">
        <slot />
    </component>
</template>

