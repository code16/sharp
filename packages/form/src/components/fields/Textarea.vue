<template>
    <textarea
        class="SharpTextarea form-control"
        :value="value"
        :rows="rows"
        :placeholder="placeholder"
        :disabled="readOnly"
        v-maxlength="maxLength"
        @input="handleInput"
    >{{ value }}</textarea>
</template>

<script>
    import { maxlength } from 'sharp/directives';

    export default {
        name:'SharpTextarea',

        props: {
            value: {
                type: String,
            },
            placeholder: String,
            readOnly: Boolean,

            maxLength: Number,

            rows: Number,
        },
        data() {
            return { }
        },
        methods: {
            handleInput(e) {
                this.$emit('input', e.target.value);
            }
        },
        directives: {
            maxlength
        }
    }
</script>
