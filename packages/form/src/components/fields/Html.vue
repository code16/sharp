<script setup lang="ts" >
    import { TemplateRenderer } from '@/components';
    import { FormHtmlFieldData } from "@/types";

    defineProps<{
        field: FormHtmlFieldData,
        value: FormHtmlFieldData['value'],
    }>();
</script>

<template>
    <TemplateRenderer
        :template="field.template"
        :template-data="value"
    />
</template>
