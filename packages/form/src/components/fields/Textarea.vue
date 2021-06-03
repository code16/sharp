<template>
    <textarea
        class="SharpTextarea form-control"
        :value.prop="value"
        :rows="rows"
        :placeholder="placeholder"
        :disabled="readOnly"
        @input="handleInput"
    ></textarea>
</template>

<script>
    import { validateTextField } from "../../util/validation";
    import { normalizeText } from "../../util/text";

    export default {
        name:'SharpTextarea',

        props: {
            value: String,
            placeholder: String,
            readOnly: Boolean,

            maxLength: Number,

            rows: Number,
        },
        methods: {
            validate(value) {
                return validateTextField(value, {
                    maxlength: this.maxLength,
                });
            },
            handleInput(e) {
                const value = normalizeText(e.target.value);
                const error = this.validate(value);
                this.$emit('input', value, { error });
            },
        },
    }
</script>
