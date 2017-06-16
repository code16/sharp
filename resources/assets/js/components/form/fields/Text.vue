<template>
    <input :type="inputType" class="SharpText"
           :value="value"  :placeholder="placeholder" :disabled="disabled"
           @input="handleInput" ref="input"
    >
</template>

<script>
    import Vue from 'vue';
    import { Focusable } from '../../../mixins';

    export default {
        name:'SharpText',

        mixins: [Focusable],

        props: {
            value: [String, Number],

            placeholder: String,
            disabled: Boolean,
            inputType:  {
                type:String,
                default:'text'
            },
        },
        data() {
            return {}
        },
        methods: {
            handleInput(e) {
                this.$emit('input',e.target.value);
            }
        },

        mounted() {
            this.setFocusable(this.$refs.input);
        }
    }
</script>