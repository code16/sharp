<template>
    <div class="ShowListField" :class="classes">
        <div class="ShowListField__label">
            {{ label }}
        </div>
        <div class="ShowListField__content">
            <template v-if="isEmpty">
                <em class="ShowListField__empty text-muted">{{ lang('show.list.empty') }}</em>
            </template>
            <template v-else>
                <div class="ShowListField__list">
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
        </div>
    </div>
</template>

<script>
    import { Grid } from 'sharp-ui';
    import { UnknownField } from 'sharp/components';
    import { lang } from 'sharp';

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
        computed: {
            isEmpty() {
                return !this.value || this.value.length === 0;
            },
            classes() {
                return {
                    'ShowListField--empty': this.isEmpty,
                }
            },
        },
        methods: {
            lang,
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