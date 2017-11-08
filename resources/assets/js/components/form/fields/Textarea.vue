<template>
    <textarea class="SharpTextarea" :rows="rows" :placeholder="placeholder" :disabled="readOnly"
              :value="value" v-maxlength="maxLength" @input="handleInput">
    </textarea>
</template>

<script>
    import Maxlength from '../../../directives/Maxlength';

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
            Maxlength
        }
    }
</script>