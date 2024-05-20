<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Input } from "@/components/ui/input";

    const props = defineProps<FormFieldProps<FormTextFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTextFieldData>>();

    const input = ref();

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
        focus: () => input.value.$el.focus(),
    });
</script>

<template>
    <FormFieldLayout v-bind="props" v-slot="{ id, ariaDescribedBy }">
        <Input
            :id="id"
            :model-value="field.localized && typeof value === 'object' ? value?.[locale] : (value as string)"
            :placeholder="field.placeholder"
            :disabled="field.readOnly"
            :has-error="hasError"
            :aria-describedby="ariaDescribedBy"
            @update:model-value="onInput"
            ref="input"
        />
    </FormFieldLayout>
</template>
