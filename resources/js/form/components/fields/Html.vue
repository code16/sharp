<script setup lang="ts" >
    import { TemplateRenderer } from '@/components';
    import { FormHtmlFieldData } from "@/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldProps } from "@/form/types";

    defineProps<FormFieldProps<FormHtmlFieldData>>();
</script>

<template>
    <FormFieldLayout v-bind="$props">
        <div class="text-sm">
            <TemplateRenderer
                :template="field.template"
                :template-data="value"
            />
        </div>
    </FormFieldLayout>
</template>
