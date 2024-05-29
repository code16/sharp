<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { useParentForm } from "@/form/useParentForm";
    import { FormFieldData, FormListFieldData, FormUploadFieldValueData } from "@/types";
    import { getDependantFieldsResetData } from "@/form/util";
    import { computed, ref, watch, watchEffect } from "vue";
    import { Button } from '@/components/ui/button';
    import ListBulkUpload from "./ListBulkUpload.vue";
    import { showAlert } from "@/utils/dialogs";
    import { FieldsMeta, FormFieldEmits, FormFieldProps } from "@/form/types";
    import { Serializable } from "@/form/Serializable";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Toggle } from "@/components/ui/toggle";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import { MoreHorizontal, GripVertical } from "lucide-vue-next";
    import {
        DropdownMenu, DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Card } from "@/components/ui/card";
    import { useSortable } from "@vueuse/integrations/useSortable";

    const props = defineProps<FormFieldProps<FormListFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormListFieldData>>();

    const form = useParentForm();
    const canAddItem = computed(() => {
        const { field, value } = props;
        return field.addable &&
            (!field.maxItemCount || value?.length < field.maxItemCount) &&
            !field.readOnly;
    });
    const isUploading = computed(() => {
        return (form.meta[props.field.key] as FieldsMeta[])
            ?.some(itemMeta => Object.values(itemMeta).some(fieldMeta => fieldMeta.uploading));
    });
    const currentBulkUploadLimit = computed(() => {
        const { field, value } = props;
        if(field.maxItemCount) {
            const remaining = field.maxItemCount - (value?.length ?? 0);
            return Math.min(remaining, field.bulkUploadLimit);
        }
        return field.bulkUploadLimit;
    });
    const dragging = ref(false);
    const reordering = ref(false);
    const sortedKey = ref(0);
    const sortableContainer = ref<HTMLElement>();
    const sortable = useSortable(
        sortableContainer,
        computed({
            get: () => props.value ?? [],
            set: (newItems) => {
                // sortedKey.value++;
                emit('input', newItems);
            }
        }),
        {
            animation: 150,
            handle: '[data-drag-handle]',
            onEnd(e) {
                const itemMeta = form.getMeta(`${props.field.key}.${e.oldIndex}`) as FieldsMeta;
                form.setMeta(props.field.key,
                    (form.meta[props.field.key] as FieldsMeta[])
                        ?.toSpliced(e.oldIndex, 1)
                        ?.toSpliced(e.newIndex, 0, itemMeta ?? {})
                );
            }
        }
    );
    watchEffect(() => {
        sortable.option('handle', reordering.value ? null : '[data-drag-handle]');
    });

    let itemKeyIndex = 0;
    const itemKey = Symbol('itemKey');
    const errorIndex = Symbol('errorIndex');

    watch(() => form.errors, () => {
        emit('input', props.value?.map(((item, index) => ({ ...item, [errorIndex]: index }))));
    });

    emit('input', props.value?.map(item => ({ ...item, [itemKey]: itemKeyIndex++ })), { force: true });

    function createItem(data = {}) {
        return {
            [props.field.itemIdAttribute]: null,
            [itemKey]: itemKeyIndex++,
            ...data,
        }
    }

    function onAdd() {
        emit('input', [...(props.value ?? []), createItem()]);
        // form.setMeta(
        //     props.field.key,
        //     [...(form.meta[props.field.key] ?? []), {}]
        // );
    }

    function onInsert(itemIndex: number) {
        emit('input', props.value.toSpliced(itemIndex, 0, createItem()));
        form.setMeta(
            props.field.key,
            (form.meta[props.field.key] as FieldsMeta[])?.toSpliced(itemIndex, 0, {})
        );
    }

    function onRemove(itemIndex: number) {
        emit('input', props.value.toSpliced(itemIndex, 1));
        form.setMeta(
            props.field.key,
            (form.meta[props.field.key] as FieldsMeta[])?.toSpliced(itemIndex, 1)
        );
    }

    function onBulkUploadInputChange(e: Event & { target: HTMLInputElement }) {
        const files = [...e.target.files].slice(0, currentBulkUploadLimit.value);

        if(e.target.files.length > currentBulkUploadLimit.value) {
            const message = __('sharp::form.list.bulk_upload.validation.limit', {
                limit: currentBulkUploadLimit.value
            });

            showAlert(message, {
                title: __('sharp::modals.error.title'),
            });
        }

        emit('input', [
            ...props.value,
            ...files.map(file => createItem({
                [props.field.bulkUploadField]: { nativeFile: file } satisfies Partial<FormUploadFieldValueData>,
            })),
        ]);

        e.target.value = '';
    }

    function onFieldInput(itemIndex: number, itemFieldKey: string, itemFieldValue: FormFieldData['value'], { force = false } = {}) {
        const newListValue = Serializable.wrap(itemFieldValue, itemFieldValue =>
            props.value.map((item, i) => {
                if(i === itemIndex) {
                    return {
                        ...item,
                        ...(!force ? getDependantFieldsResetData(props.field.itemFields, itemFieldKey) : null),
                        [itemFieldKey]: itemFieldValue,
                    }
                }
                return item;
            })
        );

        emit('input', newListValue);
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        form.setMeta(fieldKey, { locale });
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        form.setMeta(fieldKey, { uploading });
    }
</script>

<template>
    <FormFieldLayout v-bind="props" field-group>
        <template #action v-if="field.sortable && value?.length > 1">
            <Toggle
                class="h-6"
                size="sm"
                :pressed="reordering"
                :disabled="isUploading"
                @click="reordering = !reordering"
            >
                {{ __('sharp::form.list.sort_button.inactive') }}
            </Toggle>
        </template>
        <div class="grid gap-y-4">
            <template v-if="value?.length > 0">
<!--                <Card>-->
                    <div class="group/list space-y-2"
                        @dragstart="dragging = true"
                        @dragend="dragging = false"
                        :ref="(el: HTMLElement) => sortableContainer = el"
                    >
                        <template v-for="(item, index) in value" :key="`${item[itemKey]}-${sortedKey}`">
                            <Card>
                                <div class="group relative p-4 bg-background first:rounded-t-lg last:rounded-b-lg"
                                :class="[
                                    '[&.sortable-ghost]:z-10 [&.sortable-ghost]:ring-2 [&.sortable-ghost]:ring-ring [&.sortable-ghost]:ring-offset-2',
                                    reordering ? 'cursor-grab bg-muted/30' : 'bg-background'
                                ]">
                                <!--                        <template v-if="canAddItem && field.sortable && !dragActive">-->
                                <!--                            <div class="SharpList__new-item-zone">-->
                                <!--                                <Button size="sm" @click="onInsert(index)">-->
                                <!--                                    {{ __('sharp::form.list.insert_button') }}-->
                                <!--                                </Button>-->
                                <!--                            </div>-->
                                <!--                        </template>-->

                                <div :inert="reordering">
                                    <FieldGrid class="flex-1 min-w-0 gap-6">
                                        <template v-for="row in fieldLayout.item">
                                            <FieldGridRow>
                                                <template v-for="itemFieldLayout in row">
                                                    <FieldGridColumn :layout="itemFieldLayout" :class="{ '!hidden': !form.fieldShouldBeVisible(itemFieldLayout, field.itemFields, item) }">
                                                        <SharpFormField
                                                            :field="form.getField(itemFieldLayout.key, field.itemFields, item)"
                                                            :field-layout="itemFieldLayout"
                                                            :field-error-key="`${field.key}.${item[errorIndex] ?? index}.${itemFieldLayout.key}`"
                                                            :value="item[itemFieldLayout.key]"
                                                            :locale="form.getMeta(`${field.key}.${index}.${itemFieldLayout.key}`)?.locale ?? locale"
                                                            :row="row"
                                                            @input="(value, options) => onFieldInput(index, itemFieldLayout.key, value, options)"
                                                            @locale-change="onFieldLocaleChange(`${field.key}.${index}.${itemFieldLayout.key}`, $event)"
                                                            @uploading="onFieldUploading(`${field.key}.${index}.${itemFieldLayout.key}`, $event)"
                                                        />
                                                    </FieldGridColumn>
                                                </template>
                                            </FieldGridRow>
                                        </template>
                                    </FieldGrid>

                                    <DropdownMenu :modal="false">
                                        <DropdownMenuTrigger as-child>
                                            <Button class="absolute top-0 right-0" variant="ghost" size="icon">
                                                <MoreHorizontal class="w-4 h-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <DropdownMenuItem class="text-destructive" @click="onRemove(index as number)">
                                                {{ __('sharp::form.upload.remove_button') }}
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>

                                <!--                        <template v-if="field.removable && !field.readOnly && !dragActive">-->
                                <!--                            <Button-->
                                <!--                                class=""-->
                                <!--                                variant="ghost"-->
                                <!--                                size="icon"-->
                                <!--                                :aria-label="__('sharp::form.list.remove_button')"-->
                                <!--                                @click="onRemove(index)"-->
                                <!--                            >&times;</Button>-->
                                <!--                        </template>-->

                                <div class="z-10 absolute opacity-0 flex items-center justify-center left-0 top-1/2 -translate-x-1/2 -translate-y-1/2 h-4 w-3 rounded-sm border bg-border duration-300 transition-opacity cursor-grab group-[&:has([draggable=true])]/list:opacity-0 group-[&:has([draggable=true])]/list:transition-none hover:bg-foreground hover:border-foreground hover:text-background group-hover:opacity-100"
                                    data-drag-handle
                                >
                                    <div class="absolute -inset-2"></div>
                                    <GripVertical class="h-2.5 w-2.5" />
                                </div>
                                <!--                        <template v-if="field.sortable && value?.length > 1 && !isUploading">-->
                                <!--                            <div class="d-flex align-items-center px-1" data-drag-handle>-->
                                <!--                                <i class="fas fa-grip-vertical opacity-25"></i>-->
                                <!--                            </div>-->
                                <!--                        </template>-->
                            </div>
                            </Card>
                        </template>
                    </div>
<!--                </Card>-->
            </template>


<!--            <template v-if="field.itemFields[field.bulkUploadField]?.type === 'upload' && canAddItem && currentBulkUploadLimit > 0">-->
<!--                <ListBulkUpload-->
<!--                    :field="field"-->
<!--                    :current-bulk-upload-limit="currentBulkUploadLimit"-->
<!--                    :disabled="reordering"-->
<!--                    @change="onBulkUploadInputChange"-->
<!--                    key="upload"-->
<!--                />-->
<!--            </template>-->

            <template v-if="canAddItem">
                <div>
                    <Button
                        class=" w-full"
                        :disabled="field.readOnly || reordering"
                        variant="secondary"
                        @click="onAdd"
                    >
                        ＋ {{ field.addText }}
                    </Button>
                </div>
            </template>
<!--            <template v-if="field.readOnly && !value?.length">-->
<!--                <em class="SharpList__empty-alert">-->
<!--                    {{ __('sharp::form.list.empty') }}-->
<!--                </em>-->
<!--            </template>-->
        </div>
    </FormFieldLayout>
</template>
