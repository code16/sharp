<template>
    <textarea class="SharpTextarea" :rows="rows" :placeholder="placeholder" :disabled="readOnly"
              :value="value" @input="handleInput">
    </textarea>
</template>

<script>

    export default {
        name:'SharpTextarea',

        props: {
            value: {
                type: String,
            },
            placeholder: String,
            readOnly: Boolean,

            rows: Number,
        },
        data() {
            return { }
        },
        methods: {
            handleInput(e) {
                this.$emit('input', e.target.value);
            }
        }
    }
</script>