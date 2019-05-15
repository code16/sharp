<template>
    <div>
        <field-name @change-event="change"></field-name>
    </div>
</template>

<script>
    export default {
        name:'SharpBaseComponent', //Mandatory : Define the sharp component name
        props: {
            fieldKey:String, //Optional : get the current field key
            value:[/* value types */] //Mandatory
            // given field props...
        },
        methods: {
            change(val) {
                this.$emit('input', val); //Mandatory : emit input event on field value change
            }
        }
    }
</script>