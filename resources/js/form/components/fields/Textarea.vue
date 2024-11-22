<script setup lang="ts">
    import { FormTextareaFieldData } from "@/types";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";
    import { Textarea } from "@/components/ui/textarea";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormTextareaFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTextareaFieldData>>();

    function onInput(textareaValue: string) {
        const value = normalizeText(textareaValue);
        const error = validateTextField(value, {
            maxlength: props.field.maxLength,
        });
        if(props.field.localized && typeof props.value === 'object') {
            emit('input', { ...props.value, [props.locale]: value }, { error });
        } else {
            emit('input', value, { error });
        }
    }
</script>

<template>
    <FormFieldLayout v-bind="props" v-slot="{ id, ariaDescribedBy }">
        <Textarea
            :id="id"
            class="min-h-0"
            :model-value="field.localized && typeof value === 'object' ? value?.[locale] : (value as string)"
            :rows="field.rows"
            :placeholder="field.placeholder"
            :disabled="field.readOnly"
            :aria-describedby="ariaDescribedBy"
            @update:model-value="onInput"
        />
    </FormFieldLayout>
</template>
