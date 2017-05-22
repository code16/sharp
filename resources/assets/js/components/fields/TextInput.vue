<template>
    <el-input :type="inputType" :placeholder="placeholder" v-model="input" :disabled="disabled">

    </el-input>
</template>

<script>
    import { Input } from 'element-ui';


    export default {
        name:'SharpTextInput',

        components: {
            [Input.name]:Input
        },

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