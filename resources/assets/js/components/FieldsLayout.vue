<template>
    <sharp-grid :rows="layout">
        <template scope="field">
            <slot v-if="!field.item.fieldset" :item="field.item"></slot>
            <fieldset v-else>
                <legend>{{field.item.fieldset}}</legend>
                <sharp-fields-layout :layout="field.item.fields">
                    <template scope="fieldset">
                        <slot :item="fieldset.item"></slot>
                    </template>
                </sharp-fields-layout>
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
            layout: { // 2D array fields [ligne][col]
                type: Array,
                required: true
            }
        }
    }
</script>