<script setup lang="ts">
    import { FormTextareaFieldData } from "@/types";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";

    const props = defineProps<{
        field: FormTextareaFieldData,
        value: FormTextareaFieldData['value'],
        locale?: string | null,
    }>();

    const emit = defineEmits(['input']);

    function onInput(e) {
        const value = normalizeText(e.target.value);
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
    <textarea
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
        :value="field.localized && typeof value === 'object' ? value[locale] : value"
        :rows="field.rows"
        :placeholder="field.placeholder"
        :disabled="field.readOnly"
        @input="onInput"
    ></textarea>
</template>
