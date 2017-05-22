<template>
    <sharp-grid :rows="fields">
        <template scope="field">
            <sharp-field-container v-if="!field.item.fieldset"
                                   :field-key="field.item.key"
                                   :field-props="all[field.item.key]"
                                   :field-type="all[field.item.key].type"
                                   :label="all[field.item.key].label"
                                   :help-message="all[field.item.key].helpMessage"
                                   :read-only="all[field.item.key].readOnly">
            </sharp-field-container>
            <fieldset v-else>
                <legend>{{field.item.fieldset}}</legend>
                <sharp-fields-layout :fields="field.item.fields" :all="all"></sharp-fields-layout>
            </fieldset>
        </template>
    </sharp-grid>
</template>

<script>
    import Grid from './Grid';
    import FieldContainer from './FieldContainer';
    export default {
        name:'SharpFieldsLayout',

        components: {
            [FieldContainer.name]:FieldContainer,
            [Grid.name]:Grid
        },

        props: {
            fields: { // 2D array fields [ligne][col]
                type: Array,
                required: true
            },
            all: { // all fields
                type: Object,
                required: true
            }
        }
    }
</script>