<template>
    <div>
        <template v-for="option in options">
            <div class="SharpSelect__item" :key="option.id">
                <Check
                    :value="isChecked(option)"
                    :text="labels[option.id]"
                    :read-only="readOnly"
                    @input="handleCheckboxChanged($event, option)"
                />
            </div>
        </template>
        <template v-if="showSelectAll">
            <div class="SharpSelect__links mt-3">
                <div class="row mx-n2">
                    <div class="col-auto px-2">
                        <a href="#" @click.prevent="handleSelectAllClicked">{{ lang('form.select.select_all') }}</a>
                    </div>
                    <div class="col-auto px-2">
                        <a href="#" @click.prevent="handleUnselectAllClicked">{{ lang('form.select.unselect_all') }}</a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import Check from "../Check";
    import { isSelected } from "../../../util/select";
    import { lang } from "sharp";

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
        },
        methods: {
            lang,
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
                if (checked) {
                    this.$emit('input', [...(this.value ?? []), option.id])
                }
                else {
                    this.$emit('input', (this.value ?? []).filter(val => !isSelected(option, val)));
                }
            },
        }
    }
</script>
