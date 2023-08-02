<script setup lang="ts">
    import { __ } from "@/util/i18n";
</script>

<template>
    <div :class="{ 'card card-body form-control':root }">
        <div class="row gy-1 gx-3" :class="inline ? 'row-cols-auto' : 'row-cols-1'">
            <template v-for="(option, index) in options" :key="option.id">
                <div class="col">
                    <Check
                        class="mb-0"
                        :id="checkboxId(index)"
                        :value="isChecked(option)"
                        :text="labels[option.id]"
                        :read-only="readOnly"
                        @input="handleCheckboxChanged($event, option)"
                    />
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
    import { __ } from "@/util/i18n";

    export default {
        components: {
            Check,
        },
        props: {
            value: Array,
            options: Array,
            readOnly: Boolean,
            labels: Object,
            showSelectAll: Boolean,
            maxSelected: Number,
            inline: Boolean,
            root: Boolean,
            uniqueIdentifier: String,
        },
        methods: {
            isChecked(option) {
                return this.value?.some(value => isSelected(option, value));
            },
            checkboxId(index) {
                return `${this.uniqueIdentifier}.${index}`;
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
