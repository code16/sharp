<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { Eye, EyeOff } from 'lucide-vue-next';

    const props = defineProps<FormFieldProps<FormTextFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTextFieldData>>();

    const input = ref();
    const passwordVisible = ref(false);

    function onInput(inputValue: string) {
        const value = normalizeText(inputValue);
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
        <div class="relative">
            <Input
                :id="id"
                :class="field.inputType === 'password' ? 'pr-10' : ''"
                :model-value="field.localized && typeof value === 'object' ? value?.[locale] : (value as string)"
                :placeholder="field.placeholder"
                :disabled="field.readOnly"
                :aria-describedby="ariaDescribedBy"
                :type="passwordVisible ? 'text' : field.inputType"
                @update:model-value="onInput"
                ref="input"
            />
            <template v-if="field.inputType === 'password'">
                <Button class="absolute size-[2.375rem] right-px top-px rounded-[calc(var(--radius)-3px)]" size="icon" variant="ghost" @click="passwordVisible = !passwordVisible">
                    <template v-if="passwordVisible">
                        <EyeOff />
                    </template>
                    <template v-else>
                        <Eye />
                    </template>
                </Button>
            </template>
        </div>
    </FormFieldLayout>
</template>
