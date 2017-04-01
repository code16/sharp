<template>
    <div>
        <label>{{label}}</label>
        <sharp-field :field-key="fieldKey"
                     :field-type="fieldType"
                     :field-props="fieldProps"
                     :disabled="readOnly">
        </sharp-field>
        <div>{{helpMessage}}</div>
    </div>
</template>

<script>
    import Field from './Field';

    export default {
        name:'SharpFieldContainer',
        props : {
            fieldKey: {
                type: String,
                required: true
            },
            fieldType: {
                type: String
            },
            fieldProps: {
                type: Object
            },
            label: {
                type: String
            },
            helpMessage: {
                type: Array
            },
            readOnly: {
                type: Boolean
            }
        },
        components: {
            'sharp-field':Field
        }
    }
</script>