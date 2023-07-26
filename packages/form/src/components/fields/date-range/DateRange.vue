<template>
    <DatePicker
        class="SharpDateRange position-relative"
        :value="value"
        :monday-first="mondayFirst"
        :display-format="displayFormat"
        :columns="$screens({ default: 1, lg: 2 })"
        :update-on-input="updateOnInput"
        is-range
        @input="handleChanged"
        v-slot="{ inputValue, inputEvents }"
    >
        <div class="input-group" :class="{ 'input-group-sm': small }">
            <template v-if="small && !value">
                <div class="form-select form-control" tabindex="0" v-on="inputEvents.start">
                    <span></span>
                </div>
            </template>
            <template v-else>
                <input
                    class="form-control border-end-0"
                    :value="inputValue.start"
                    :placeholder="startPlaceholder"
                    :disabled="readOnly"
                    v-bind="$props"
                    autocomplete="off"
                    v-on="inputEvents.start"
                />
                <div class="form-control SharpDateRange__dash px-0 border-start-0 border-end-0">
                    -
                </div>
                <input
                    class="form-control border-start-0"
                    :class="{ 'clearable': hasClearButton }"
                    :value="inputValue.end"
                    :placeholder="endPlaceholder"
                    :disabled="readOnly"
                    v-bind="$props"
                    autocomplete="off"
                    v-on="inputEvents.end"
                />
            </template>
        </div>
        <template v-if="hasClearButton">
            <ClearButton @click="handleClearClicked" />
        </template>
    </DatePicker>
</template>

<script>
    import { lang } from "sharp";
    import { ClearButton } from "@sharp/ui";
    import DatePicker from "../date/DatePicker.vue";

    export default {
        components: {
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
            updateOnInput: {
                type: Boolean,
                default: true,
            },
        },
        computed: {
           hasClearButton() {
               return this.clearable && !!this.value?.start && !!this.value?.end;
           },
        },
        methods: {
            $screens: ()=> {},
            handleChanged(value) {
                if(value?.start?.toDateString() === this.oldValue?.start?.toDateString()
                    && value?.end?.toDateString() === this.oldValue?.end?.toDateString()
                ) {
                    return;
                }
                this.$emit('input', value);
                this.oldValue = value;
            },
            handleClearClicked() {
                this.$emit('input', null);
            },
            focus() {
                setTimeout(() => {
                    this.$el.querySelector('.form-control').focus();
                });
            },
        },
    }
</script>
