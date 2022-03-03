<template>
    <v-date-picker
        class="vc-datepicker"
        :value="value"
        :popover="popoverOptions"
        :is-range="isRange"
        :update-on-input="false"
        :locale="locale"
        :first-day-of-week="firstDayOfWeek"
        color="primary"
        is24hr
        trim-weeks
        @input="handleInput"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template v-slot:default="props">
            <slot v-bind="props" />
        </template>
    </v-date-picker>
</template>

<script>
    import { DatePicker as VDatePicker } from 'v-calendar';

    export default {
        name: 'SharpDatePicker',
        components: {
            VDatePicker,
        },
        props: {
            value: [Date, Object],
            isRange: Boolean,
            mondayFirst: Boolean,
            displayFormat: String,
        },
        computed: {
            popoverOptions() {
                const boundary = document.querySelector('[data-popover-boundary]');
                return {
                    visibility: 'focus', // 'click' to debug
                    hideDelay: 10,
                    placement: 'bottom',
                    modifiers: [
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary,
                            },
                        },
                        {
                            name: 'flip',
                            enabled: false,
                        },
                    ],
                }
            },
            masks() {
                return {
                    input: this.displayFormat || undefined,
                }
            },
            locale() {
                return document.documentElement.lang;
            },
            firstDayOfWeek() {
                return this.mondayFirst ? 2 : 1;
            },
        },
        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
        },
    }
</script>
