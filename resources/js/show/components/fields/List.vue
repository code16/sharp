<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowListFieldData } from "@/types";
    import { UnknownField } from '@/components';
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
                const visibleFields = Object.values(props.field.itemFields).filter(field =>
                    show.fieldShouldBeVisible(field, item[field.key], props.locale)
                );
                return visibleFields.length === 1 && visibleFields[0].type === 'file';
            })
    });
</script>

<template>
    <ShowFieldLayout v-bind="props">
        <template v-if="value?.length > 0">
            <div :class="hasOnlyOneVisibleFileField ? 'space-y-4' : 'space-y-6'">
                <template v-for="item in value">
                    <Card :class="hasOnlyOneVisibleFileField ? 'shadow-none border-none bg-none' : ''">
                        <CardContent :class="hasOnlyOneVisibleFileField ? '!p-0' : ''">
                            <FieldGrid class="gap-6">
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
                                                        :hide-label="hasOnlyOneVisibleFileField"
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
            <div class="text-muted-foreground">
                {{ __('sharp::show.list.empty') }}
            </div>
        </template>
    </ShowFieldLayout>
</template>
