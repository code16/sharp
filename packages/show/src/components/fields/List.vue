<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import ShowField from "../Field.vue";
    import { ShowListFieldData } from "@/types";
    import { Grid } from '@sharp/ui';
    import { UnknownField } from 'sharp/components';
    import FieldLayout from "../FieldLayout.vue";
    import { FieldProps } from "../types";

    defineProps<FieldProps & {
        field: ShowListFieldData,
        value: ShowListFieldData['value'],
    }>()
</script>

<template>
    <FieldLayout class="ShowListField" :label="field.label">
        <div class="ShowListField__content">
            <template v-if="value?.length > 0">
                <div class="ShowListField__list">
                    <template v-for="item in value">
                        <div class="ShowListField__item">
                            <Grid class="ShowListField__fields-grid" :rows="layout.item" v-slot="{ itemLayout:fieldLayout }">
                                <template v-if="field.itemFields?.[fieldLayout.key]">
                                    <ShowField
                                        v-bind="$props"
                                        :field="field.itemFields?.[fieldLayout.key]"
                                        :value="item[fieldLayout.key]"
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
            <template v-else>
                <em class="ShowListField__empty text-muted">
                    {{ __('sharp::show.list.empty') }}
                </em>
            </template>
        </div>
    </FieldLayout>
</template>
