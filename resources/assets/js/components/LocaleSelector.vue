<template>
    <sharp-multiselect
        class="SharpLocaleSelector d-inline-block"
        :class="errorClasses"
        :options="locales"
        :searchable="false"
        :allow-empty="false"
        :value="value"
        @input="$emit('input',$event)"
    >
        <div slot="option" slot-scope="props" :class="optionClasses(props.option)">{{ props.option }}</div>
    </sharp-multiselect>
</template>

<script>
    import SharpMultiselect from './Multiselect';

    export default {
        name: 'SharpLocaleSelector',
        components: {
            SharpMultiselect
        },
        props: {
            locales: Array,
            value: String,
            errors: Object
        },
        computed: {
            hasLocaleErrors() {
                return this.errors && !!Object.keys(this.errors).length;
            },
            errorClasses() {
                return this.hasError(this.value) ? 'SharpLocaleSelector--has-error' : this.hasLocaleErrors ? 'SharpLocaleSelector--has-partial-error' : '';
            }
        },
        methods: {
            optionClasses(locale) {
                return this.hasError(locale) ? 'error-dot' : '';
            },
            hasError(locale) {
                return this.errors && this.errors[locale];
            }
        }
    }
</script>