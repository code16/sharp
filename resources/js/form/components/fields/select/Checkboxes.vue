<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormSelectFieldData } from "@/types";
    import { FormFieldProps } from "@/form/types";
    import { isSelected } from "@/form/util/select";
    import { Checkbox } from "@/components/ui/checkbox";

    const props = defineProps<FormFieldProps<FormSelectFieldData> & { labels: { [id: string]: string } }>();

    function validate(value: typeof props.value) {
        if(props.field.maxSelected && Array.isArray(value) && value?.length > props.field.maxSelected) {
            return __('sharp::form.select.validation.max_selected', { max_selected: props.field.maxSelected });
        }
        return null;
    }

    function onChange(checked, option: FormSelectFieldData['options'][0]) {
        const value = checked
            ? [...(this.value ?? []), option.id]
            : (this.value ?? []).filter(val => !isSelected(option, val));

        const error = this.validate(value);
        this.$emit('input', value, { error });
    }
</script>

<template>
    <div :class="{ 'border bg-white rounded':root }">
        <div class="flex gap-2" :class="field.inline ? 'flex-row' : 'flex-col'">
            <template v-for="(option, index) in field.options" :key="option.id">
                <div>
                    <Checkbox
                        class="mb-0"
                        :id="`${fieldErrorKey}.${index}`"
                        :checked="value?.some(value => isSelected(option, value))"
                        :disabled="field.readOnly"
                        @update:checked="onChange($event, option)"
                    >
                        {{ labels[option.id] }}
                    </Checkbox>
                </div>
            </template>
        </div>

        <template v-if="field.showSelectAll">
            <div class="SharpSelect__links mt-3">
                <div class="row gx-3">
                    <div class="col-auto">
                        <a href="#" @click.prevent="$emit('input', field.options.map(o => o.id))">
                            {{ __('sharp::form.select.select_all') }}
                        </a>
                    </div>
                    <div class="col-auto">
                        <a href="#" @click.prevent="$emit('input', null)">
                            {{ __('sharp::form.select.unselect_all') }}
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
