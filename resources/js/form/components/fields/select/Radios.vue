<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";
    import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
    import { Label } from "@/components/ui/label";
    import { isSelected } from "@/form/util/select";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormSelectFieldData, string | number | null>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();
</script>

<template>
    <FormFieldLayout class="gap-y-3.5" v-bind="props" field-group v-slot="{ id }">
        <RadioGroup
            class="flex items-start gap-y-1 gap-x-6"
            :class="field.inline ? 'flex-row flex-wrap' : 'flex-col'"
            :model-value="value != null ? String(value) : null"
            @update:model-value="checkedRadioValue => $emit('input', field.options.find(o => isSelected(o, checkedRadioValue))?.id)"
        >
            <template v-for="(option, index) in field.options" :key="option.id">
                <div class="group/control flex items-center space-x-3">
                    <RadioGroupItem
                        class="peer"
                        :id="`${id}.${index}`"
                        :value="String(option.id)"
                        :disabled="field.readOnly"
                    />
                    <Label class="font-normal py-1 text-sm leading-4" :for="`${id}.${index}`">
                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                    </Label>
                </div>
            </template>
        </RadioGroup>
    </FormFieldLayout>
</template>
