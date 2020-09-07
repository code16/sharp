<template>
    <FieldLayout class="ShowListField" :class="classes" :label="label">
        <div class="ShowListField__content">
            <template v-if="isEmpty">
                <em class="ShowListField__empty text-muted">{{ lang('show.list.empty') }}</em>
            </template>
            <template v-else>
                <div class="ShowListField__list">
                    <template v-for="item in value">
                        <div class="ShowListField__item">
                            <Grid class="ShowListField__fields-grid" :rows="layout.item" v-slot="{ itemLayout:fieldLayout }">
                                <template v-if="fieldOptions(fieldLayout)">
                                    <ShowField
                                        :options="fieldOptions(fieldLayout)"
                                        :value="fieldValue(item, fieldLayout)"
                                        :config-identifier="fieldLayout.key"
                                    />
                                </template>
                                <template v-else>
                                    <UnknownField :name="fieldLayout.key" />
                                </template>
                            </Grid>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </FieldLayout>
</template>

<script>
    import { lang } from 'sharp';
    import { Grid } from 'sharp-ui';
    import { UnknownField } from 'sharp/components';
    import { syncVisibility } from "../../util/fields/visiblity";
    import FieldLayout from "../FieldLayout";

    export default {
        components: {
            Grid,
            UnknownField,
            FieldLayout,
        },
        props: {
            value: Array,
            itemFields: {
                type: Object,
                required: true,
            },
            layout: Object,
            label: String,
            emptyVisible: Boolean,
        },
        computed: {
            isEmpty() {
                return !this.value || this.value.length === 0;
            },
            isVisible() {
                return !this.isEmpty || this.emptyVisible;
            },
            classes() {
                return {
                    'ShowListField--empty': this.isEmpty,
                }
            },
            fileFieldsCollapsed() {
                const fileValues = this.allValuesOfType('file');
                return fileValues.every(value => value && !value.thumbnail);
            },
        },
        methods: {
            lang,
            fieldOptions(layout) {
                const options = this.itemFields
                    ? { ...this.itemFields[layout.key] }
                    : null;
                if(!options) {
                    console.error(`Show list field: unknown field "${layout.key}"`);
                }
                if(options.type === 'file') {
                    options.collapsed = this.fileFieldsCollapsed;
                }
                return options;
            },
            fieldValue(item, layout) {
                return item ? item[layout.key] : null;
            },
            allValuesOfType(fieldType) {
                return (this.value || []).reduce((res, item) => [
                    ...res,
                    ...Object.entries(item)
                        .filter(([key]) => {
                            const options = this.itemFields[key];
                            return options && options.type === fieldType;
                        })
                        .map(([key, value]) => value)
                ], []);
            },
        },
        created() {
            syncVisibility(this, () => this.isVisible);
        }
    }
</script>