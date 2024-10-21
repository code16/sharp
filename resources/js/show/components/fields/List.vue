<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowListFieldData } from "@/types";
    import { UnknownField } from '@/components';
    import FieldLayout from "../FieldLayout.vue";
    import { ShowFieldProps } from "../../types";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { useParentShow } from "@/show/useParentShow";
    import { Card, CardContent } from "@/components/ui/card";
    import { computed } from "vue";

    const props = defineProps<ShowFieldProps<ShowListFieldData>>();
    const show = useParentShow();
    const hasOnlyOneVisibleFile = computed(() => {
        return props.field.itemFields
            && Object.values(props.field.itemFields).filter(field => field.type === 'file').length === 1
            && props.value?.every(item => Object.values(props.field.itemFields).filter(field =>
                show.fieldShouldBeVisible(field, item[field.key], props.locale)
            ))
    });
</script>

<template>
    <FieldLayout class="ShowListField" :label="field.label">
        <template v-if="value?.length > 0">
            <div :class="hasOnlyOneVisibleFile ? 'space-y-4' : 'space-y-6'">
                <template v-for="item in value">
                    <Card :class="hasOnlyOneVisibleFile ? 'shadow-none border-none bg-none' : ''">
                        <CardContent :class="hasOnlyOneVisibleFile ? '!p-0' : ''">
                            <FieldGrid class="gap-x-4 gap-y-4">
                                <template v-for="row in fieldLayout.item">
                                    <FieldGridRow>
                                        <template v-for="itemFieldLayout in row">
                                            <FieldGridColumn
                                                :layout="itemFieldLayout"
                                                v-show="field.itemFields?.[itemFieldLayout.key] && show.fieldShouldBeVisible(field.itemFields[itemFieldLayout.key], item[itemFieldLayout.key], locale)"
                                            >
                                                <template v-if="field.itemFields?.[itemFieldLayout.key]">
                                                    <SharpShowField
                                                        v-bind="$props"
                                                        :field="field.itemFields?.[itemFieldLayout.key]"
                                                        :value="item[itemFieldLayout.key]"
                                                    />
                                                </template>
                                                <template v-else>
                                                    <UnknownField :name="itemFieldLayout.key" />
                                                </template>
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
        <template v-else>
            <em class="ShowListField__empty text-muted">
                {{ __('sharp::show.list.empty') }}
            </em>
        </template>
    </FieldLayout>
</template>
