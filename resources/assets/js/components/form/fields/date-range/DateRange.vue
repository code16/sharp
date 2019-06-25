<template>
    <el-date-picker
        class="SharpDateRange"
        :value="transformedValue"
        :format="transformedFormat"
        :start-placeholder="startPlaceholder"
        :end-placeholder="endPlaceholder"
        :disabled="readOnly"
        :clearable="clearable"
        type="daterange"
        popper-class="SharpDateRange__popper"
        @change="handleChanged"
    />
</template>

<script>
    import { lang } from "../../../../mixins/Localization";
    import ElDatePicker from 'element-ui/lib/date-picker';

    export default {
        components: {
            ElDatePicker,
        },
        props: {
            value: {
                type: Object,
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
                default: lang('form.daterange.start_placeholder'),
            },
            endPlaceholder: {
                type: String,
                default: lang('form.daterange.end_placeholder'),
            },
            clearable: {
                type: Boolean,
                default: true,
            },
            readOnly: Boolean,
        },
        computed: {
            transformedValue() {
                const value = this.value || {};
                return [
                    value.start,
                    value.end,
                ];
            },
            transformedFormat() {
                return this.displayFormat
                    .replace(/D/g, 'd')
                    .replace(/Y/g, 'y');
            },
        },
        methods: {
            handleChanged(value) {
                const range = value || [];
                this.$emit('input', {
                    start: range[0],
                    end: range[1],
                });
            },
        },
    }
</script>