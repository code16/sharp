<script lang="ts" setup>
    import { Card, CardContent } from "@/components/ui/card";
    import { computed } from "vue";
    import { Badge } from "@/components/ui/badge";

    const props = defineProps<{
        templateData?: Record<string, any>,
        templateProps?: string[],
        template: string,
    }>();

    const component = computed(() => ({
        components: {
            'sharp-card': Card,
            'sharp-card-content': CardContent,
            'sharp-badge': Badge,
        },
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

