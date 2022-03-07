<template>
    <DatePicker
        class="position-relative"
        :value="value"
        :monday-first="mondayFirst"
        :display-format="displayFormat"
        :columns="$screens({ default: 1, lg: 2 })"
        is-range
        @input="handleChanged"
        v-slot="{ inputValue, inputEvents }"
    >
        <div class="input-group" :class="{ 'input-group-sm': small }">
            <DateRangeInput
                v-bind="$props"
                :value="inputValue.start"
                :placeholder="startPlaceholder"
                v-on="inputEvents.start"
            />
            <DateRangeInput
                :class="{ 'clearable': hasClearButton }"
                v-bind="$props"
                :value="inputValue.end"
                :placeholder="endPlaceholder"
                v-on="inputEvents.end"
            />
        </div>
        <template v-if="hasClearButton">
            <ClearButton @click="handleClearClicked" />
        </template>
    </DatePicker>
</template>

<script>
    import { lang } from "sharp";
    import { ClearButton } from "sharp-ui";
    import DatePicker from "../date/DatePicker";
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
            small: Boolean,
        },
        computed: {
           hasClearButton() {
               return this.clearable && !!this.value?.start && !!this.value?.end;
           },
        },
        methods: {
            handleChanged(value) {
                if(value?.start?.toDateString() === this.oldValue?.start?.toDateString()
                    && value?.end?.toDateString() === this.oldValue?.end?.toDateString()
                ) {
                    return;
                }
                this.$emit('input', value);
                this.oldValue = value;
            },
            handleFocused() {
                this.$emit('focus');
            },
            handleBlur() {
                this.$emit('blur');
            },
            handleClearClicked() {
                this.$emit('input', null);
            },
        },
    }
</script>
