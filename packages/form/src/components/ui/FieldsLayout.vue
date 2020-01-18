<template>
    <Grid :rows="layout">
        <template slot-scope="fieldLayout">
            <slot v-if="!fieldLayout.legend" v-bind="fieldLayout"></slot>

            <fieldset class="SharpForm__fieldset" v-else v-show="isFieldsetVisible(fieldLayout)">
                <div class="SharpModule__inner">
                    <div class="SharpModule__header">
                        <legend class="SharpModule__title">{{fieldLayout.legend}}</legend>
                    </div>
                    <div class="SharpModule__content">
                        <sharp-fields-layout :layout="fieldLayout.fields">
                            <template slot-scope="fieldset">
                                <slot v-bind="fieldset"></slot>
                            </template>
                        </sharp-fields-layout>
                    </div>
                </div>
            </fieldset>
        </template>
    </Grid>
</template>

<script>
    import { Grid } from 'sharp/components';

    export default {
        name:'SharpFieldsLayout',

        components: {
            Grid
        },

        props: {
            layout: { // 2D array fields [ligne][col]
                type: Array,
                required: true
            },
            visible : {
                type: Object,
                default: () => ({})
            }
        },

        data() {
            return {
                fieldsetMap: {}
            }
        },

        methods: {
            isFieldsetVisible(fieldLayout) {
                let { id, fields } = fieldLayout;

                let map = this.fieldsetMap[id] || (this.fieldsetMap[id] = [].concat.apply([],fields));
                return map.some(f => this.visible[f.key]);
            }
        }
    }
</script>