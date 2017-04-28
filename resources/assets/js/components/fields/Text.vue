<template>
    <input v-if="inputType==='text'" class="form-control"  type="text" :placeholder="placeholder" :disabled="disabled">
</template>

<script>
    export default {
        name:'SharpText',

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
            return {
                input: this.value
            }
        }
    }

</script>