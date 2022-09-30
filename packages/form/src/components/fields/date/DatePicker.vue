<template>
    <v-date-picker
        class="SharpDatePicker"
        :value="value"
        :popover="popoverOptions"
        :is-range="isRange"
        :locale="locale"
        :first-day-of-week="firstDayOfWeek"
        :attributes="attributes"
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
                    visibility: 'focus',
                    // visibility: 'click', // debug
                    hideDelay: 10,
                    placement: 'bottom',
                    modifiers: [
                    //     {
                    //         name: 'preventOverflow',
                    //         options: {
                    //             boundary,
                    //         },
                    //     },
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
            attributes() {
                return [
                    {
                        key: 'today',
                        dot: true,
                        dates: new Date(),
                    }
                ];
            },
        },
        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
        },
    }
</script>
