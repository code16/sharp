<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { useParentForm } from "@/form/useParentForm";
    import { FormFieldData, FormListFieldData, FormUploadFieldValueData } from "@/types";
    import { getDependantFieldsResetData } from "@/form/util";
    import { computed, ref } from "vue";
    // import Draggable from 'vuedraggable';
    import { Button } from '@/components/ui/button';
    import ListBulkUpload from "./ListBulkUpload.vue";
    import { showAlert } from "@/utils/dialogs";
    import { FieldsMeta, FormFieldEmits, FormFieldProps } from "@/form/types";
    import { Serializable } from "@/form/Serializable";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Toggle } from "@/components/ui/toggle";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { useDraggable } from "vue-draggable-plus";
    import { EntityListInstance } from "@/entity-list/types";
    import FieldGrid from "@/components/ui/FieldGrid.vue";

    const props = defineProps<FormFieldProps<FormListFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormListFieldData>>();

    const form = useParentForm();
    const dragActive = ref(false);
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
    const sortableContainer = ref<HTMLElement>();
    const sortable = useDraggable<EntityListInstance>(
        sortableContainer as any,
        computed<EntityListInstance[]>({
            get: () => props.value ?? [],
            set: (newItems) => emit('input', newItems)
        }),
        {
            immediate: false,
        }
    );

    let itemKeyIndex = 0;
    const itemKey = Symbol('itemKey');

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
        <div>
            <div class="SharpList__sticky-wrapper text-end">
                <template v-if="field.sortable && value?.length > 1">
                    <Toggle
                        class="SharpList__sort-button"
                        size="sm"
                        :pressed="dragActive"
                        :disabled="isUploading"
                        style="pointer-events: auto"
                        @click="dragActive = !dragActive"
                    >
                        {{ __('sharp::form.list.sort_button.inactive') }}
                        <svg style="margin-left: .5em" width="1.125em" height="1.125em" viewBox="0 0 24 22" fill-rule="evenodd">
                            <path d="M20 14V0h-4v14h-4l6 8 6-8zM4 8v14h4V8h4L6 0 0 8z"></path>
                        </svg>
                    </Toggle>
                </template>
            </div>

            <div :ref="(el) => sortableContainer = el">
                <template v-for="(item, index) in value">
                    <div data-item>
                        <template v-if="canAddItem && field.sortable && !dragActive">
                            <div class="SharpList__new-item-zone">
                                <Button size="sm" @click="onInsert(index)">
                                    {{ __('sharp::form.list.insert_button') }}
                                </Button>
                            </div>
                        </template>

                        <FieldGrid>
                            <template v-for="row in fieldLayout.item">
                                <FieldGridRow>
                                    <template v-for="itemFieldLayout in row">
                                        <FieldGridColumn :layout="itemFieldLayout" v-show="form.fieldShouldBeVisible(itemFieldLayout, field.itemFields, item)">
                                            <SharpFormField
                                                :field="form.getField(itemFieldLayout.key, field.itemFields, item, dragActive)"
                                                :field-layout="itemFieldLayout"
                                                :field-error-key="`${field.key}.${index}.${itemFieldLayout.key}`"
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

                        <template v-if="field.removable && !field.readOnly && !dragActive">
                            <Button
                                class=""
                                variant="ghost"
                                size="icon"
                                :aria-label="__('sharp::form.list.remove_button')"
                                @click="onRemove(index)"
                            >&times;</Button>
                        </template>

                        <template v-if="field.sortable && value?.length > 1 && !isUploading">
                            <div class="d-flex align-items-center px-1" data-drag-handle>
                                <i class="fas fa-grip-vertical opacity-25"></i>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <template v-if="field.itemFields[field.bulkUploadField]?.type === 'upload' && canAddItem && currentBulkUploadLimit > 0">
                <ListBulkUpload
                    :field="field"
                    :current-bulk-upload-limit="currentBulkUploadLimit"
                    :disabled="dragActive"
                    @change="onBulkUploadInputChange"
                    key="upload"
                />
            </template>

            <template v-if="canAddItem">
                <div>
                    <Button
                        class="SharpList__add-button w-full"
                        :disabled="field.readOnly || dragActive"
                        variant="secondary"
                        @click="onAdd"
                    >
                        ＋ {{ field.addText }}
                    </Button>
                </div>
            </template>
            <template v-if="field.readOnly && !value?.length">
                <em class="SharpList__empty-alert">
                    {{ __('sharp::form.list.empty') }}
                </em>
            </template>
        </div>
    </FormFieldLayout>
</template>
