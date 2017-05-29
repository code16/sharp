<template>
    <sharp-grid :rows="[columns]">
        <template scope="column">
            <sharp-fields-layout v-if="fields" :layout="column.fields">
                <template scope="fieldLayout">
                    <sharp-field-display :field-key="fieldLayout.key"
                                         :context-fields="fields"
                                         :context-data="data"
                                         :field-layout="fieldLayout"
                                         :update-data="updateData">
                    </sharp-field-display>
                </template>
            </sharp-fields-layout>
        </template>
    </sharp-grid>
</template>

<script>
    import Grid from './Grid';
    import FieldsLayout from './FieldsLayout';

    export default {
        name: 'SharpFormTabContent',
        components: {
            [Grid.name]:Grid,
            [FieldsLayout.name]:FieldsLayout
        },
        props : {
            columns: Array, fields: Object, data: Object, updateData:Function
        }
    }
</script>