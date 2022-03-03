<template>
    <DatePicker
        class="input-group"
        :value="value"
        :monday-first="mondayFirst"
        :display-format="displayFormat"
        is-range
        @input="handleChanged"
        v-slot="{ inputValue, inputEvents }"
    >
        <template v-if="hasClearButton">
            <ClearButton />
        </template>
        <DateRangeInput
            v-bind="$props"
            :value="inputValue.start"
            :placeholder="startPlaceholder"
            v-on="inputEvents.start"
        />
        <DateRangeInput
            class="clearable"
            v-bind="$props"
            :value="inputValue.end"
            :placeholder="endPlaceholder"
            v-on="inputEvents.end"
        />
    </DatePicker>
</template>

<script>
    import { lang } from "sharp";
    import { ClearButton } from "sharp-ui";
    import DatePicker from "../date/Datepicker";
    import DateRangeInput from "./DateRangeInput";

    export default {
        components: {
            DateRangeInput,
            DatePicker,
            ClearButton,
        },
        props: {
            value: {
                default: () => ({
                    start: null,
                    end: null,
                }),
            },
            displayFormat: {
                type: String,
                default: 'DD/MM/YYYY',
            },
            startPlaceholder: {
                type: String,
                default: () => lang('form.daterange.start_placeholder'),
            },
            endPlaceholder: {
                type: String,
                default: () => lang('form.daterange.end_placeholder'),
            },
            clearable: {
                type: Boolean,
                default: true,
            },
            readOnly: Boolean,
            mondayFirst: Boolean,
        },
        computed: {
           hasClearButton() {
               return this.clearable && !!this.value?.start && !!this.value?.end;
           },
        },
        methods: {
            handleChanged(value) {
                this.$emit('input', value);
            },
            handleFocused() {
                this.$emit('focus');
            },
            handleBlur() {
                this.$emit('blur');
            },
        },
    }
</script>
