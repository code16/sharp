<template>
    <input
        class="SharpText"
        :type="inputType"
        :value="value"
        :placeholder="placeholder"
        :disabled="readOnly"
        v-maxlength="maxLength"
        @input="handleInput"
        @change="handleChanged"
        ref="input"
    >
</template>

<script>
    import { Focusable } from 'sharp/mixins';
    import { maxlength } from 'sharp/directives';

    export default {
        name:'SharpText',

        mixins: [Focusable],

        props: {
            value: [String, Number],

            placeholder: String,
            readOnly: Boolean,

            maxLength:Number,

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
                this.$emit('input', e.target.value);
            },
            handleChanged(e) {
                this.$emit('change', e.target.value);
            },
        },

        mounted() {
            this.setFocusable(this.$refs.input);
        },

        directives: {
            maxlength
        }
    }
</script>
