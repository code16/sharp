<template>
    <div>
        <field-name @change-event="change"></field-name>
    </div>
</template>

<script>
    export default {
        name:'SharpBaseComponent', //Mandatory : Define the sharp component name
        inject:['value'], //Mandatory : get value data
        props: {
            value: { //Optional
                type: //value types
            }
            // ...
        },
        methods: {
            change(val) {
                this.$emit('input', val); //Mandatory : emit input event on field value change
            }
        }
    }
</script>