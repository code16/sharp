<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";
    import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
    import { Label } from "@/components/ui/label";
    import { isSelected } from "@/form/util/select";

    const props = defineProps<FormFieldProps<FormSelectFieldData, string | number | null>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();
</script>

<template>
    <div  class="mt-1" :class="{
        // 'border rounded p-4': root
     }">
        <RadioGroup
            class="flex gap-2"
            :class="field.inline ? 'flex-row' : 'flex-col'"
            :model-value="value != null ? String(value) : null"
            @update:model-value="checkedRadioValue => $emit('input', field.options.find(o => isSelected(o, checkedRadioValue))?.id)"
        >
            <template v-for="(option, index) in field.options" :key="option.id">
                <div class="flex items-center space-x-2">
                    <RadioGroupItem
                        :id="`${fieldErrorKey}.${index}`"
                        :value="String(option.id)"
                        :disabled="field.readOnly"
                    />
                    <Label class="font-normal" :for="`${fieldErrorKey}.${index}`">
                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                    </Label>
                </div>
            </template>
        </RadioGroup>
    </div>
</template>
