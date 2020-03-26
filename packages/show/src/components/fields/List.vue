<template>
    <div class="ShowListField">
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
            </div>
        </template>
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