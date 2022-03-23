<template>
    <Grid :rows="layout" v-slot="{ itemLayout:fieldLayout }">
        <template v-if="isFieldset(fieldLayout)">
            <fieldset v-show="isFieldsetVisible(fieldLayout)">
                <legend class="SharpForm__label form-label">{{ fieldLayout.legend }}</legend>

                <div class="card SharpForm__fieldset shadow-sm">
                    <div class="card-body">
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
            visible: {
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
            },
            updateLayout() {
                const rows = this.$el.querySelectorAll(':scope > .SharpGrid__row');
                rows.forEach(row => {
                    const hasLabel = !!row.querySelector(':scope > .SharpGrid__col > .SharpForm__form-item--has-label');
                    const hasLocaleSelect = !!row.querySelector('.SharpFieldLocaleSelect');
                    row.classList.toggle('SharpGrid__row--has-label', hasLabel);
                    row.classList.toggle('SharpGrid__row--has-locale-select', hasLocaleSelect);
                });
            },
        },
        mounted() {
            this.updateLayout();
        },
        updated() {
            this.updateLayout();
        },
    }
</script>
