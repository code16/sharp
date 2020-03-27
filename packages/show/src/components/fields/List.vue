<template>
    <div class="ShowListField">
        <div class="ShowListField__label mb-3">
            {{ label }}
        </div>
        <div class="ShowListField__content">
            <template v-for="item in value">
                <div class="ShowListField__item">
                    <Grid class="ShowListField__fields-grid" :rows="layout.item">
                        <template slot-scope="fieldLayout">
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
                        </template>
                    </Grid>
                    <hr>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Grid } from 'sharp-ui';
    import { UnknownField } from 'sharp/components';

    export default {
        components: {
            Grid,
            UnknownField,
        },
        props: {
            value: Array,
            itemFields: {
                type: Object,
                required: true,
            },
            layout: Object,
            label: String,
        },
        methods: {
            fieldOptions(layout) {
                const options = this.itemFields
                    ? this.itemFields[layout.key]
                    : null;
                if(!options) {
                    console.error(`Show list field: unknown field "${layout.key}"`);
                }
                return options;
            },
            fieldValue(item, layout) {
                return item ? item[layout.key] : null;
            }
        }
    }
</script>