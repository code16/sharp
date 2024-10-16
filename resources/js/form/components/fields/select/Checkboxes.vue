<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormSelectFieldData } from "@/types";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { isSelected } from "@/form/util/select";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Button } from "@/components/ui/button";
    import { Label } from "@/components/ui/label";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormSelectFieldData, Array<string | number> | null>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();

    function validate(value: typeof props.value) {
        if(props.field.maxSelected && value?.length > props.field.maxSelected) {
            return __('sharp::form.select.validation.max_selected', { max_selected: props.field.maxSelected });
        }
        return null;
    }

    function onChange(checked: boolean, option: typeof props.field.options[0]) {
        const value = props.field.options
            .filter(o => o.id === option.id ? checked : props.value?.some(val => isSelected(o, val)))
            .map(o => o.id);
        emit('input', value, { error: validate(value) });
    }
</script>

<template>
    <FormFieldLayout
        v-bind="props"
        field-group
        v-slot="{ id }"
    >
        <div>
            <div class="flex items-start gap-y-1.5 gap-x-6" :class="field.inline ? 'flex-row' : 'flex-col'">
                <template v-for="(option, index) in field.options" :key="option.id">
                    <div class="group/control flex items-center space-x-3">
                        <Checkbox
                            :id="`${id}.${index}`"
                            :checked="value?.some(v => isSelected(option, v))"
                            :disabled="field.readOnly"
                            @update:checked="onChange($event, option)"
                        />
                        <Label class="font-normal py-1" :for="`${id}.${index}`">
                            {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                        </Label>
                    </div>
                </template>
            </div>

            <template v-if="field.showSelectAll">
                <div class="mt-4 flex gap-2">
                    <Button variant="link" @click="$emit('input', field.options.map(o => o.id))">
                        {{ __('sharp::form.select.select_all') }}
                    </Button>
                    <Button variant="link" @click="$emit('input', [])">
                        {{ __('sharp::form.select.unselect_all') }}
                    </Button>
                </div>
            </template>
        </div>
    </FormFieldLayout>
</template>
