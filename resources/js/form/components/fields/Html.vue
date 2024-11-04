<script setup lang="ts" >
    import { FormHtmlFieldData } from "@/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldProps } from "@/form/types";

    const props = defineProps<FormFieldProps<FormHtmlFieldData>>();
</script>

<template>
    <FormFieldLayout v-bind="props">
        <div class="text-sm" v-html="props.value">
        </div>
    </FormFieldLayout>
</template>
