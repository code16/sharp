<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormSelectFieldData } from "@/types";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { isSelected } from "@/form/util/select";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Label } from "@/components/ui/label";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Separator } from "@/components/ui/separator";
    import { useSelect } from "@/form/components/fields/select/useSelect";

    const props = defineProps<FormFieldProps<FormSelectFieldData, Array<string | number> | null>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();

    const { validate, isAllSelected, selectAll } = useSelect(props, emit);

    function onChange(checked: boolean, option: typeof props.field.options[0]) {
        const value = props.field.options
            .filter(o => o.id === option.id ? checked : props.value?.some(val => isSelected(o, val)))
            .map(o => o.id);
        emit('input', value, { error: validate(value) });
    }
</script>

<template>
    <FormFieldLayout
        class="gap-y-3.5"
        v-bind="props"
        field-group
        v-slot="{ id }"
    >
        <div>
            <div class="flex items-start gap-y-1.5 gap-x-6" :class="field.inline ? 'flex-row flex-wrap' : 'flex-col'">
                <template v-for="(option, index) in field.options" :key="option.id">
                    <div class="group/control flex items-center space-x-3">
                        <Checkbox
                            :id="`${id}.${index}`"
                            :model-value="value?.some(v => isSelected(option, v))"
                            :disabled="field.readOnly"
                            @update:model-value="onChange($event as boolean, option)"
                        />
                        <Label class="font-normal py-1 text-sm leading-4" :for="`${id}.${index}`">
                            {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                        </Label>
                    </div>
                </template>
            </div>
            <template v-if="props.field.showSelectAll">
                <Separator class="my-1.5" />
                <div class="group/control flex items-center space-x-3">
                    <Checkbox
                        :id="`${id}.select-all`"
                        :model-value="isAllSelected"
                        :disabled="field.readOnly"
                        :aria-label="isAllSelected ? __('sharp::form.select.unselect_all') : __('sharp::form.select.select_all')"
                        @update:model-value="$event ? selectAll() : emit('input', null)"
                    />
                    <Label class="font-normal py-1 text-sm leading-4" :for="`${id}.select-all`">
                        {{ isAllSelected ? __('sharp::form.select.unselect_all') : __('sharp::form.select.select_all') }}
                    </Label>
                </div>
            </template>
        </div>
    </FormFieldLayout>
</template>
