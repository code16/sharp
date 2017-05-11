<template>
    <input :type="inputType" class="form-control"
           :value="value"  :placeholder="placeholder" :disabled="disabled"
           @input="handleInput"
    >
</template>

<script>
    export default {
        name:'SharpText',

        inject: ['value'],

        props: {
            value: {
                type: String,
            },
            placeholder: String,
            disabled: Boolean,
            inputType: {
                type: String,
                default: 'text'
            }
        },
        data() {
            return {}
        },
        methods: {
            handleInput(e) {
                this.$emit('input',e.target.value);
            }
        }
    }

</script>