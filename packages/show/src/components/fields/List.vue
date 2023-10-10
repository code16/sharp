<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import ShowField from "../Field.vue";
    import { LayoutFieldData, ShowListFieldData } from "@/types";
    import { UnknownField } from 'sharp/components';
    import FieldLayout from "../FieldLayout.vue";
    import { FieldProps } from "../types";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";

    defineProps<FieldProps & {
        field: ShowListFieldData,
        value: ShowListFieldData['value'],
        layout: LayoutFieldData,
    }>()
</script>

<template>
    <FieldLayout class="ShowListField" :label="field.label">
        <template v-if="value?.length > 0">
            <div class="border divide-y">
                <template v-for="item in value">
                    <div class="px-4 py-2">
                        <FieldGrid class="gap-x-4 gap-y-4">
                            <template v-for="row in layout.item">
                                <FieldGridRow>
                                    <template v-for="fieldLayout in row">
                                        <FieldGridColumn :layout="fieldLayout">
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
                                        </FieldGridColumn>
                                    </template>
                                </FieldGridRow>
                            </template>
                        </FieldGrid>
                    </div>
                </template>
            </div>
        </template>
        <template v-else>
            <em class="ShowListField__empty text-muted">
                {{ __('sharp::show.list.empty') }}
            </em>
        </template>
    </FieldLayout>
</template>
