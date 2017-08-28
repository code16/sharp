<template>
    <sharp-grid :rows="layout">
        <template scope="fieldLayout">
            <slot v-if="!fieldLayout.legend" v-bind="fieldLayout"></slot>

            <fieldset class="SharpForm__fieldset" v-else>
                <div class="SharpModule__inner">
                    <div class="SharpModule__header">
                        <legend class="SharpModule__title">{{fieldLayout.legend}}</legend>
                    </div>
                    <div class="SharpModule__content">
                        <sharp-fields-layout :layout="fieldLayout.fields">
                            <template scope="fieldset">
                                <slot v-bind="fieldset"></slot>
                            </template>
                        </sharp-fields-layout>
                    </div>
                </div>
            </fieldset>
        </template>
    </sharp-grid>
</template>

<script>
    import Grid from '../Grid';

    export default {
        name:'SharpFieldsLayout',

        components: {
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