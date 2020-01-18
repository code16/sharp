<template>
    <input :type="inputType" class="SharpText"
           :value="value"  :placeholder="placeholder" :disabled="readOnly"
           v-maxlength="maxLength"
           @input="handleInput" ref="input"
    >
</template>

<script>
    import { Focusable } from '../../../mixins';
    import Maxlength from '../../../directives/Maxlength';

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
                this.$emit('input',e.target.value);
            }
        },

        mounted() {
            this.setFocusable(this.$refs.input);
        },

        directives: {
            Maxlength
        }
    }
</script>