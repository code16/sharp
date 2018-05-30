<template>
    <div class="d-flex align-items-center">
        <i class="SharpTextIcon__icon fa" :class="icon"></i>
        <input class="SharpText SharpTextIcon" :value="value" @change="handleChanged">
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

<style type="text/css" scoped>
    .SharpTextIcon {
        flex-grow: 1;
    }
    .SharpTextIcon__icon {
        margin-right: .5em;
    }
</style>