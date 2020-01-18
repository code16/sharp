<template>
    <div class="SharpFilterDateRange" :class="classes">
        <SharpFilterControl :opened="opened" :label="label" :no-caret="noCaret" @click="handleClicked">
            <SharpDateRange
                class="SharpFilterDateRange__field"
                :value="value"
                :display-format="displayFormat"
                :monday-first="mondayFirst"
                :clearable="!required"
                @input="handleInput"
                @focus="handlePickerFocused"
                @blur="handlePickerBlur"
                ref="range"
            />
        </SharpFilterControl>
    </div>
</template>

<script>
    import SharpFilterControl from '../FilterControl';
    import SharpDateRange from '../../form/fields/date-range/DateRange';

    export default {
        name: 'SharpFilterDateRange',

        components: {
            SharpDateRange,
            SharpFilterControl,
        },

        props: {
            value: {
                required: true,
            },
            required: Boolean,
            displayFormat: String,
            mondayFirst: Boolean,
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
            noCaret() {
                return !!this.value && !this.required;
            },
            classes() {
                return {
                    'SharpFilterDateRange--empty': this.empty,
                }
            },
        },

        methods: {
            handleClicked() {
                this.$refs.range.$refs.picker.focus();
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