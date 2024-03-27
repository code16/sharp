<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import CheckInput from "../check/CheckInput.vue";
    import { FormSelectFieldData } from "@/types";

    defineProps<{
        field: FormSelectFieldData,
        value: FormSelectFieldData['value'],
        fieldErrorKey: string,
        root: boolean,
    }>()
</script>

<template>
    <div :class="{ 'border bg-white rounded':root }">
        <div class="row gy-1 gx-3" :class="field.inline ? 'row-cols-auto' : 'row-cols-1'">
            <template v-for="(option, index) in field.options" :key="option.id">
                <div class="col">
                    <CheckInput
                        class="mb-0"
                        :id="`${fieldErrorKey}.${index}`"
                        :checked="isChecked(option)"
                        :disabled="field.readOnly"
                        @change="handleCheckboxChanged($event, option)"
                    >
                        {{ labels[option.id] }}
                    </CheckInput>
                </div>
            </template>
        </div>

        <template v-if="showSelectAll">
            <div class="SharpSelect__links mt-3">
                <div class="row gx-3">
                    <div class="col-auto">
                        <a href="#" @click.prevent="handleSelectAllClicked">{{ __('sharp::form.select.select_all') }}</a>
                    </div>
                    <div class="col-auto">
                        <a href="#" @click.prevent="handleUnselectAllClicked">{{ __('sharp::form.select.unselect_all') }}</a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script lang="ts">
    import Check from "../Check.vue";
    import { isSelected } from "../../../util/select";
    import { __ } from "@/utils/i18n";

    export default {
        components: {
            Check,
        },
        methods: {
            isChecked(option) {
                return this.value?.some(value => isSelected(option, value));
            },
            handleSelectAllClicked() {
                this.$emit('input', this.options.map(option => option.id));
            },
            handleUnselectAllClicked() {
                this.$emit('input', []);
            },
            handleCheckboxChanged(checked, option) {
                const value = checked
                    ? [...(this.value ?? []), option.id]
                    : (this.value ?? []).filter(val => !isSelected(option, val));

                const error = this.validate(value);
                this.$emit('input', value, { error });
            },
            validate(value) {
                if(this.maxSelected && value?.length > this.maxSelected) {
                    return __('sharp::form.select.validation.max_selected', { max_selected: this.maxSelected });
                }
                return null;
            },
        }
    }
</script>
