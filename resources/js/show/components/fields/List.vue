<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowListFieldData } from "@/types";
    import ShowFieldLayout from "../ShowFieldLayout.vue";
    import { ShowFieldProps } from "../../types";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { useParentShow } from "@/show/useParentShow";
    import { Card, CardContent } from "@/components/ui/card";
    import { computed } from "vue";

    const props = defineProps<ShowFieldProps<ShowListFieldData>>();
    const show = useParentShow();
    const hasOnlyOneVisibleFileField = computed(() => {
        return props.field.itemFields
            && props.value?.every(item => {
                const visibleFields = props.fieldLayout.item.flat().filter(fieldLayout =>
                    show.fieldShouldBeVisible(fieldLayout, props.locale, props.field.itemFields, item)
                );
                return visibleFields.length === 1 && props.field.itemFields[visibleFields[0].key]?.type === 'file';
            });
    });
</script>

<template>
    <ShowFieldLayout v-bind="props">
        <template v-if="value?.length > 0">
            <template v-if="hasOnlyOneVisibleFileField">
                <div class="grid grid-cols-1 gap-y-4">
                    <template v-for="item in value">
                        <template v-for="row in fieldLayout.item">
                            <template v-for="itemFieldLayout in row">
                                <SharpShowField
                                    v-if="show.fieldShouldBeVisible(itemFieldLayout, props.locale, props.field.itemFields, item)"
                                    v-bind="props"
                                    :field="field.itemFields[itemFieldLayout.key]"
                                    :value="item[itemFieldLayout.key]"
                                    hide-label
                                />
                            </template>
                        </template>
                    </template>
                </div>
            </template>
            <template v-else>
                <div class="grid grid-cols-1 gap-y-6">
                    <template v-for="item in value">
                        <Card>
                            <CardContent>
                                <FieldGrid class="gap-6">
                                    <template v-for="row in fieldLayout.item">
                                        <FieldGridRow v-show="show.fieldRowShouldBeVisible(row, props.locale, props.field.itemFields, item)">
                                            <template v-for="itemFieldLayout in row">
                                                <FieldGridColumn
                                                    :layout="itemFieldLayout"
                                                    v-show="show.fieldShouldBeVisible(itemFieldLayout, props.locale, props.field.itemFields, item)"
                                                >
                                                    <SharpShowField
                                                        v-bind="props"
                                                        :field="field.itemFields?.[itemFieldLayout.key]"
                                                        :value="item[itemFieldLayout.key]"
                                                        :field-layout="itemFieldLayout"
                                                        :row="row"
                                                    />
                                                </FieldGridColumn>
                                            </template>
                                        </FieldGridRow>
                                    </template>
                                </FieldGrid>
                            </CardContent>
                        </Card>
                    </template>
                </div>
            </template>
        </template>
        <template v-else>
            <div class="text-muted-foreground">
                {{ __('sharp::show.list.empty') }}
            </div>
        </template>
    </ShowFieldLayout>
</template>
