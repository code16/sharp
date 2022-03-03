<template>
    <div class="SharpFilterDateRange" :class="classes">
        <FilterControl :opened="opened" :label="label" @click="handleClicked">
            <DateRange
                class="SharpFilterDateRange__field input-group-sm"
                :value="value"
                :display-format="displayFormat"
                :monday-first="mondayFirst"
                :clearable="!required"
                :read-only="disabled"
                @input="handleInput"
                @focus="handlePickerFocused"
                @blur="handlePickerBlur"
                ref="range"
            />
<!--            <template v-if="required || !value">-->
<!--                <div class="form-control dropdown-toggle"></div>-->
<!--            </template>-->
        </FilterControl>
    </div>
</template>

<script>
    import { DateRange } from 'sharp-form';
    import FilterControl from '../FilterControl';

    export default {
        name: 'SharpFilterDateRange',

        components: {
            DateRange,
            FilterControl,
        },

        props: {
            value: {
                required: true,
            },
            required: Boolean,
            displayFormat: String,
            mondayFirst: Boolean,
            disabled: Boolean,
            label: String,
        },

        data() {
            return {
                opened: false,
            }
        },

        computed: {
            empty() {
                return !this.value;
            },
            classes() {
                return {
                    'SharpFilterDateRange--empty': this.empty,
                }
            },
        },

        methods: {
            handleClicked() {

            },
            handleInput(range) {
                this.$emit('input', range);
            },
            handlePickerFocused() {
                this.opened = true;
            },
            handlePickerBlur() {
                this.opened = false;
            },
        }
    }
</script>
