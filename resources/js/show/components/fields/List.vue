<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowListFieldData } from "@/types";
    import { UnknownField } from '@/components';
    import FieldLayout from "../FieldLayout.vue";
    import { ShowFieldProps } from "../../types";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";

    defineProps<ShowFieldProps<ShowListFieldData>>();
</script>

<template>
    <FieldLayout class="ShowListField" :label="field.label">
        <template v-if="value?.length > 0">
            <div class="border divide-y">
                <template v-for="item in value">
                    <div class="px-4 py-2">
                        <FieldGrid class="gap-x-4 gap-y-4">
                            <template v-for="row in fieldLayout.item">
                                <FieldGridRow>
                                    <template v-for="itemFieldLayout in row">
                                        <FieldGridColumn :layout="itemFieldLayout">
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
