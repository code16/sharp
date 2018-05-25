<template>
    <div>
        <input class="SharpText" :value="value" @change="handleChanged">
        <i class="fa" :class="icon"></i>
    </div>
</template>

<script>
    export default {
        props: {
            value: String, // field value
            icon: String   // custom added props (given in field definition)
        },
        methods: {
            handleChanged(e) {
                this.$emit('input', e.target.value); // emit input when the value change, form data is updated
            }
        }
    }
</script>