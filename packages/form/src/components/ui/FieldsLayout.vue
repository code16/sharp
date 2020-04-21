<template>
    <Grid :rows="layout" v-slot="{ itemLayout:fieldLayout }">
        <template v-if="isFieldset(fieldLayout)">
            <fieldset class="SharpForm__fieldset" v-show="isFieldsetVisible(fieldLayout)">
                <div class="SharpModule__inner">
                    <div class="SharpModule__header">
                        <div class="SharpModule__title">{{fieldLayout.legend}}</div>
                    </div>
                    <div class="SharpModule__content">
                        <FieldsLayout :layout="fieldLayout.fields" v-slot="{ fieldLayout }">
                            <slot :field-layout="fieldLayout" />
                        </FieldsLayout>
                    </div>
                </div>
            </fieldset>
        </template>
        <template v-else>
            <slot :field-layout="fieldLayout" />
        </template>
    </Grid>
</template>

<script>
    import { Grid } from 'sharp-ui';

    export default {
        name: 'FieldsLayout',

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
            isFieldset(fieldLayout) {
                return !!fieldLayout.legend;
            },
            isFieldsetVisible(fieldsetLayout) {
                return (fieldsetLayout.fields || []).flat()
                    .some(fieldLayout => this.visible[fieldLayout.key]);
            }
        }
    }
</script>