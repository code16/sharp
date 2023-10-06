<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { useForm } from "../../../useForm";
    import FieldColumn from "@/components/ui/FieldColumn.vue";
    import Field from "../../Field.vue";
    import { FormFieldData, FormListFieldData, LayoutFieldData } from "@/types";
    import { getDependantFieldsResetData } from "../../../util";
    import { computed, ref, on } from "vue";
    import Draggable from 'vuedraggable';
    import { Button } from "@sharp/ui";
    import ListBulkUpload from "./ListBulkUpload.vue";
    import { showAlert } from "@/utils/dialogs";
    import { FieldsMeta } from "../../../types";
    import { SortableOptions } from "sortablejs";

    const props = defineProps<{
        field: FormListFieldData,
        fieldLayout: LayoutFieldData,
        value: FormListFieldData['value'],
        locale?: string | null,
    }>();

    const emit = defineEmits(['input']);
    const form = useForm();
    const dragging = ref(false);
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

    function onReorder(value: FormListFieldData['value']) {
        emit('input', value);
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
                [props.field.bulkUploadField]: { file },
            })),
        ]);

        e.target.value = '';
    }

    function onFieldInput(itemIndex: number, fieldKey: string, value: FormFieldData['value'], { force = false } = {}) {
        emit('input', props.value.map((item, i) => {
            if(i === itemIndex) {
                return {
                    ...item,
                    ...(!force ? getDependantFieldsResetData(props.field.itemFields, fieldKey) : null),
                    [fieldKey]: value,
                }
            }
            return item;
        }));
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        form.setMeta(fieldKey, { locale });
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        form.setMeta(fieldKey, { uploading });
    }
</script>

<template>
    <div class="SharpList"
        :class="{
            'SharpList--dragging': dragging,
        }"
        @dragstart="dragging = true"
        @dragend="dragging = false"
    >
        <div class="SharpList__sticky-wrapper text-end">
            <template v-if="field.sortable && value?.length > 1">
                <Button
                    class="SharpList__sort-button"
                    text
                    small
                    :active="dragActive"
                    :disabled="isUploading"
                    style="pointer-events: auto"
                    @click="dragActive = !dragActive"
                >
                    {{ __('sharp::form.list.sort_button.inactive') }}
                    <svg style="margin-left: .5em" width="1.125em" height="1.125em" viewBox="0 0 24 22" fill-rule="evenodd">
                        <path d="M20 14V0h-4v14h-4l6 8 6-8zM4 8v14h4V8h4L6 0 0 8z"></path>
                    </svg>
                </Button>
            </template>
        </div>

        <Draggable
            v-bind="{
                handle: dragActive ? '[data-item]' : '[data-drag-handle]',
                animation: 150,
                // filter: '.SharpListUpload',
            } as SortableOptions"
            :modelValue="value"
            :item-key="item => item[itemKey]"
            @update:modelValue="onReorder"
        >
            <template #item="{ element: itemData, index }">
                <div class="SharpList__item list-group-item"
                    :class="{'SharpList__item--drag-active': dragActive}"
                    data-item
                >
                    <template v-if="canAddItem && field.sortable && !dragActive">
                        <div class="SharpList__new-item-zone">
                            <Button small @click="onInsert(index)">
                                {{ __('sharp::form.list.insert_button') }}
                            </Button>
                        </div>
                    </template>

                    <template v-for="row in fieldLayout.item">
                        <div class="flex flex-wrap -mx-4">
                            <template v-for="itemFieldLayout in row">
                                <FieldColumn class="px-4" :layout="itemFieldLayout" v-show="form.fieldShouldBeVisible(itemFieldLayout, field.itemFields, itemData)">
                                    <Field
                                        :field="form.getField(itemFieldLayout.key, field.itemFields, itemData, dragActive)"
                                        :field-layout="itemFieldLayout"
                                        :field-error-key="`${field.key}.${index}.${itemFieldLayout.key}`"
                                        :value="itemData[itemFieldLayout.key]"
                                        :locale="form.getMeta(`${field.key}.${index}.${itemFieldLayout.key}`)?.locale ?? locale"
                                        @input="(value, options) => onFieldInput(index, itemFieldLayout.key, value, options)"
                                        @locale-change="onFieldLocaleChange(`${field.key}.${index}.${itemFieldLayout.key}`, $event)"
                                        @uploading="onFieldUploading(`${field.key}.${index}.${itemFieldLayout.key}`, $event)"
                                    />
                                </FieldColumn>
                            </template>
                        </div>
                    </template>

                    <template v-if="field.removable && !field.readOnly && !dragActive">
                        <button
                            class="SharpList__remove-button btn-close"
                            :aria-label="__('sharp::form.list.remove_button')"
                            @click="onRemove(index)"
                        >&times;</button>
                    </template>

                    <template v-if="field.sortable && value?.length > 1 && !isUploading">
                        <div class="d-flex align-items-center px-1" data-drag-handle>
                            <i class="fas fa-grip-vertical opacity-25"></i>
                        </div>
                    </template>
                </div>
            </template>

            <template #footer>
                <template v-if="field.itemFields[field.bulkUploadField]?.type === 'upload' && canAddItem && currentBulkUploadLimit > 0">
                    <ListBulkUpload
                        :field="field"
                        :current-bulk-upload-limit="currentBulkUploadLimit"
                        :disabled="dragActive"
                        @change="onBulkUploadInputChange"
                        key="upload"
                    />
                </template>
            </template>
        </Draggable>
        <template v-if="canAddItem">
            <div class="mt-3">
                <Button class="SharpList__add-button" :disabled="field.readOnly || dragActive" text block @click="onAdd">
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
</template>
