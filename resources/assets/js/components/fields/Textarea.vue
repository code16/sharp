<template>
    <textarea name="" id="" cols="30" rows="10"></textarea>
    <!--<el-input type="textarea" :rows="rows"-->
              <!--:placeholder="placeholder"-->
              <!--v-model="textarea" :disabled="disabled">-->
    <!--</el-input>-->
</template>

<script>

    export default {
        name:'SharpTextarea',

        props: {
            value: {
                type: String,
            },
            placeholder: String,
            disabled: Boolean,

            rows: Number
        },
        data() {
            return {
                textarea: this.value
            }
        }
    }
</script>