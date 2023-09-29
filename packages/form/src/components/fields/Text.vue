<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";
    import { ExclamationCircleIcon } from '@heroicons/vue/20/solid'
    import TextInput from "./text/TextInput.vue";
    import FieldErrorIcon from "../FieldErrorIcon.vue";

    const props = defineProps<{
        field: FormTextFieldData,
        value: FormTextFieldData['value'],
        locale?: string | null,
        hasError: boolean,
    }>();

    const input = ref();
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

    defineExpose({
        focus: () => input.value.focus(),
    });
</script>

<script lang="ts">
    export default { inheritAttrs: false }
</script>

<template>
    <div class="relative rounded-md shadow-sm">
        <TextInput
            :value="field.localized && typeof value === 'object' ? value?.[locale] : value"
            :placeholder="field.placeholder"
            :disabled="field.readOnly"
            :has-error="hasError"
            v-bind="$attrs"
            @input="onInput"
            ref="input"
        />
        <template v-if="hasError">
            <FieldErrorIcon />
        </template>
    </div>
</template>
