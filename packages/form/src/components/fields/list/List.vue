<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { useParentForm } from "../../../useParentForm";
    import FieldColumn from "@/components/ui/FieldColumn.vue";
    import Field from "../../Field.vue";
    import { FormFieldData, FormListFieldData, LayoutFieldData } from "@/types";
    import { getDependantFieldsResetData } from "../../../util";
    import { computed, ref } from "vue";
    import Draggable from 'vuedraggable';
    import { TemplateRenderer } from 'sharp/components';
    import { Button } from "@sharp/ui";
    import ListUpload from "./ListUpload.vue";
    import { showAlert } from "@/utils/dialogs";
    import { FieldsMeta } from "../../../types";

    const props = defineProps<{
        field: FormListFieldData,
        fieldLayout: LayoutFieldData,
        value: FormListFieldData['value'],
        locale: string | null,
    }>();

    const emit = defineEmits(['input']);
    const form = useParentForm();
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

    const uploadLimit = computed(() => {
        if(props.field.maxItemCount) {
            const remaining = props.field.maxItemCount - (props.value?.length ?? 0);
            return Math.min(remaining, props.field.bulkUploadLimit);
        }
        return props.field.bulkUploadLimit;
    });

    function onAdd() {
        emit('input', [...(props.value ?? []), { [props.field.itemIdAttribute]: null }]);
    }

    function onInsert(itemIndex: number) {
        emit('input', [...props.value].splice(itemIndex, 0, { [props.field.itemIdAttribute]: null }));
        (form.meta[props.field.key] as FieldsMeta[])?.splice(itemIndex, 0, {});
    }

    function onRemove(itemIndex: number) {
        emit('input', [...props.value].splice(itemIndex, 1));
        (form.meta[props.field.key] as FieldsMeta[])?.splice(itemIndex, 1);
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

    function onBulkUpload(e: Event & { target: HTMLInputElement }) {
        const files = [...e.target.files].slice(0, uploadLimit.value);

        if(e.target.files.length > uploadLimit.value) {
            const message = __('sharp::form.list.bulk_upload.validation.limit', {
                limit: uploadLimit.value
            });

            showAlert(message, {
                title: __('sharp::modals.error.title'),
            });
        }

        emit('input', [
            ...props.value,
            ...files.map(file => ({
                [props.field.itemIdAttribute]: null,
                [props.field.bulkUploadField]: { file },
            })),
        ]);
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
            :options="{
                handle: dragActive ? '[data-item]' : '[data-drag-handle]',
                // filter: '.SharpListUpload',
            }"
            :list="value"
            tag="transition-group"
            name="expand"
            ref="draggable"
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

                    <template v-if="dragActive && field.collapsedItemTemplate">
                        <TemplateRenderer
                            :template="field.collapsedItemTemplate"
                            :template-data="{ $index: index, ...itemData }"
                        />
                    </template>
                    <template v-else>
                        <template v-for="row in fieldLayout.item">
                            <div class="flex -mx-4">
                                <template v-for="itemFieldLayout in row">
                                    <FieldColumn class="px-4" :layout="itemFieldLayout">
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
                                @click="onRemove(index)"
                                :aria-label="__('sharp::form.list.remove_button')"
                            ></button>
                        </template>
                    </template>

                    <template v-if="field.sortable && value?.length > 1 && !isUploading">
                        <div class="d-flex align-items-center px-1" data-drag-handle>
                            <i class="fas fa-grip-vertical opacity-25"></i>
                        </div>
                    </template>
                </div>
            </template>

            <template #footer>
                <template v-if="field.itemFields[field.bulkUploadField]?.type === 'upload' && canAddItem && uploadLimit > 0">
                    <ListUpload
                        :field="field.itemFields[field.bulkUploadField]"
                        :limit="uploadLimit"
                        :disabled="dragActive"
                        @change="onBulkUpload"
                        key="upload"
                    />
                </template>
                <template v-if="canAddItem">
                    <div class="mt-3">
                        <Button class="SharpList__add-button" :disabled="field.readOnly || dragActive" text block @click="onAdd">
                            ＋ {{ field.addText }}
                        </Button>
                    </div>
                </template>
            </template>
        </Draggable>
        <template v-if="field.readOnly && !value?.length">
            <em class="SharpList__empty-alert">
                {{ __('sharp::form.list.empty') }}
            </em>
        </template>
    </div>
</template>
