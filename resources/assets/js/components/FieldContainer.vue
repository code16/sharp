<template>
    <div class="form-group">
        <label v-html="label"></label>
        <sharp-field :field-key="fieldKey"
                     :field-type="fieldType"
                     :field-props="fieldProps"
                     :value="value"
                     :disabled="readOnly">
        </sharp-field>
        <div>{{helpMessage}}</div>
    </div>
</template>

<script>
    import Field from './Field';
    import { FieldValue } from '../mixins';

    export default {
        name:'SharpFieldContainer',
        components: {
            [Field.name]:Field
        },
        mixins: [FieldValue],
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

            fieldLayout: {
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
        }
    }
</script>