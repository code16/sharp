<template>
    <v-date-picker
        :value="value"
        :popover="popoverOptions"
        :is-range="isRange"
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
        },
        computed: {
            popoverOptions() {
                return {
                    visibility: 'focus',
                    hideDelay: 10,
                    modifiers: [
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary: document.querySelector('[data-popover-boundary]'),
                            },
                        },
                    ],
                }
            },
        },
        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
        },
    }
</script>
