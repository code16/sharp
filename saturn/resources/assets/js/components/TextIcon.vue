<template>
    <div class="SharpTextIcon">
        <div class="input-group">
            <div class="input-group-text bg-transparent">
                <i class="SharpTextIcon__icon fa" :class="icon"></i>
            </div>
            <input class="form-control" :value="value" @change="handleChanged">
        </div>
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

<style scoped>
    .SharpTextIcon__icon {
        font-size: 1.125em;
    }
</style>
