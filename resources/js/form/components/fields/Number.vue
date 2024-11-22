<script setup lang="ts">
    import { FormNumberFieldData } from "@/types";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { Input } from "@/components/ui/input";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormNumberFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormNumberFieldData>>();
</script>

<template>
    <FormFieldLayout v-bind="props">
        <Input
            :class="{
                '[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none': !field.showControls,
            }"
            :model-value="value"
            :step="field.step"
            :min="field.min"
            :max="field.max"
            :disabled="field.readOnly"
            type="number"
            @update:model-value="emit('input', $event !== '' ? Number($event) : null)"
        />
    </FormFieldLayout>
</template>
