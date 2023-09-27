<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";

    const props = defineProps<{
        field: FormTextFieldData,
        value: FormTextFieldData['value'],
        locale: string | null,
    }>();

    const input = ref();
    const emit = defineEmits(['input']);

    function onInput(e) {
        const value = normalizeText(e.target.value);
        const error = validateTextField(value, {
            maxlength: props.field.maxLength,
        });
        emit('input', value, { error });
    }

    defineExpose({
        focus: () => input.value.focus(),
    });
</script>

<template>
    <input
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6"
        :value="field.localized ? value[locale] : value"
        :placeholder="field.placeholder"
        :disabled="field.readOnly"
        @input="onInput"
        ref="input"
    >
</template>
