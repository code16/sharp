<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { useParentForm } from "@/form/useParentForm";
    import { FormFieldData, FormListFieldData, FormUploadFieldData, FormUploadFieldValueData } from "@/types";
    import { getDependantFieldsResetData } from "@/form/util";
    import { computed, nextTick, ref, watch, watchEffect } from "vue";
    import { Button, buttonVariants } from '@/components/ui/button';
    import { showAlert } from "@/utils/dialogs";
    import { FieldMeta, FieldsMeta, FormFieldEmits, FormFieldProps } from "@/form/types";
    import FieldGridRow from "@/components/ui/FieldGridRow.vue";
    import FieldGridColumn from "@/components/ui/FieldGridColumn.vue";
    import { Toggle } from "@/components/ui/toggle";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import FieldGrid from "@/components/ui/FieldGrid.vue";
    import { MoreHorizontal, GripVertical, ArrowUpDown } from "lucide-vue-next";
    import {
        DropdownMenu, DropdownMenuContent,
        DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { Card, CardHeader } from "@/components/ui/card";
    import { useSortable } from "@vueuse/integrations/useSortable";
    import { watchArray } from "@vueuse/core";

    const props = defineProps<FormFieldProps<FormListFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormListFieldData>>();

    const form = useParentForm();
    const canAddItem = computed(() => {
        const { field, value } = props;
        return field.addable &&
            (!field.maxItemCount || value?.length < field.maxItemCount) &&
            !field.readOnly;
    });
    const hasItemDropdown = computed(() => !props.field.readOnly && (canAddItem.value && props.field.sortable || props.field.removable));
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
    const reordering = ref(false);
    const sortedKey = ref(0);
    const sortableContainer = ref<HTMLElement>();
    const sortable = useSortable(
        sortableContainer,
        computed({
            get: () => props.value ?? [],
            set: (newItems) => {
                sortedKey.value++;
                emit('input', newItems);
            }
        }),
        {
            animation: 150,
            handle: '[data-drag-handle]',
        }
    );
    watch(sortableContainer, () => {
        if(props.field.sortable) {
            sortable.stop();
            sortable.start();
        }
    });
    watchEffect(() => {
        sortable.option('handle', reordering.value ? null : '[data-drag-handle]');
    });

    let itemKeyIndex = 0;
    const itemKey = Symbol('itemKey') as unknown as string;
    const errorIndex = Symbol('errorIndex') as unknown as string;

    watch(() => form.errors, () => {
        emit('input', props.value?.map(((item, index) => ({ ...item, [errorIndex]: index }))));
    });

    emit('input', props.value?.map(item => ({ ...item, [itemKey]: itemKeyIndex++ })), { force: true });

    watch(form.meta, () => console.log(form.meta), { deep: true });

    watchArray(() => props.value, async (newList, oldList, added) => {
        if(!added.length) {
            // for remove / sort we wait after child fields has triggered unmount
            await nextTick();
        }
        form.setMeta(
            props.field.key,
            props.value?.map(item =>
                (form.meta[props.field.key] as FieldsMeta[])?.find(meta => meta[itemKey] === item[itemKey])
                ?? ({ [itemKey]: item[itemKey] })
            ) ?? []
        );
    });

    function createItem(data = {}) {
        return {
            [props.field.itemIdAttribute]: null,
            [itemKey]: itemKeyIndex++,
            ...data,
        }
    }

    async function onAdd() {
        emit('input', [...(props.value ?? []), createItem()]);
    }

    async function onInsert(itemIndex: number) {
        emit('input', props.value.toSpliced(itemIndex, 0, createItem()));
    }

    async function onRemove(itemIndex: number) {
        form.clearErrors(`${props.fieldErrorKey}.${itemIndex}`);
        emit('input', props.value.toSpliced(itemIndex, 1));
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
        emit('input', props.value.map((item, i) => {
            if(i === itemIndex) {
                return {
                    ...item,
                    ...(!force ? getDependantFieldsResetData(props.field.itemFields, itemFieldKey) : null),
                    [itemFieldKey]: itemFieldValue,
                }
            }
            return item;
        }));
    }

    function onFieldLocaleChange(fieldKey: string, locale: string) {
        form.setMeta(fieldKey, { locale }, props.field.key);
    }

    function onFieldUploading(fieldKey: string, uploading: boolean) {
        form.setMeta(fieldKey, { uploading }, props.field.key);
    }

    function itemShouldHavePaddingTop(item: FormListFieldData['value'][0]) {
        return hasItemDropdown.value
            && props.fieldLayout.item[0]?.length === 1
            && !props.field.itemFields[props.fieldLayout.item[0][0].key].label;
    }

    const bulkDroppingFile = ref(false);
</script>

<template>
    <FormFieldLayout
        v-bind="props"
        field-group
        sticky-label
    >
        <template #action v-if="field.sortable && !field.readOnly">
            <Toggle
                class="h-6 gap-2"
                :class="{ 'invisible': value?.length < 2 }"
                size="sm"
                :model-value="reordering"
                :disabled="isUploading"
                @click="reordering = !reordering"
            >
                <ArrowUpDown class="size-4 opacity-50" />
                {{ reordering ? __('sharp::form.list.sort_button.active') : __('sharp::form.list.sort_button.inactive') }}
            </Toggle>
        </template>
        <div class="grid gap-y-6">
            <template v-if="value?.length > 0">
                <div class="relative group/list space-y-6" :ref="(el: HTMLElement) => sortableContainer = el">
                    <TransitionGroup move-class="transition-transform duration-200" leave-to-class="opacity-0" leave-active-class="!absolute" :css="false">
                        <template v-for="(item, index) in value" :key="`${item[itemKey]}-${sortedKey}`">
                            <Card class="group relative ring-ring ring-offset-2 ring-background p-6"
                                :class="[
                                    '[&.sortable-ghost]:z-10 [&.sortable-ghost]:ring-2',
                                    reordering ? 'cursor-grab bg-muted/50' : 'bg-background',
                                    itemShouldHavePaddingTop(item) ? 'pt-10' : ''
                                ]"
                            >
                                <div :inert="reordering">
                                    <FieldGrid class="flex-1 min-w-0 gap-6">
                                        <template v-for="row in fieldLayout.item">
                                            <FieldGridRow v-show="form.fieldRowShouldBeVisible(row, field.itemFields, item)">
                                                <template v-for="itemFieldLayout in row">
                                                    <FieldGridColumn
                                                        :layout="itemFieldLayout"
                                                        v-show="form.fieldShouldBeVisible(itemFieldLayout, field.itemFields, item)"
                                                    >
                                                        <SharpFormField
                                                            :field="form.getField(itemFieldLayout.key, field.itemFields, item, props.field.readOnly)"
                                                            :field-layout="itemFieldLayout"
                                                            :field-error-key="`${field.key}.${item[errorIndex] ?? item[itemKey]}.${itemFieldLayout.key}`"
                                                            :parent-field="field"
                                                            :value="item[itemFieldLayout.key]"
                                                            :locale="(form.getMeta(`${field.key}.${item[itemKey]}.${itemFieldLayout.key}`) as FieldMeta)?.locale ?? form.defaultLocale"
                                                            :row="row"
                                                            @input="(value, options) => onFieldInput(index, itemFieldLayout.key, value, options)"
                                                            @locale-change="onFieldLocaleChange(`${field.key}.${item[itemKey]}.${itemFieldLayout.key}`, $event)"
                                                            @uploading="onFieldUploading(`${field.key}.${item[itemKey]}.${itemFieldLayout.key}`, $event)"
                                                        />
                                                    </FieldGridColumn>
                                                </template>
                                            </FieldGridRow>
                                        </template>
                                    </FieldGrid>

                                    <template v-if="hasItemDropdown">
                                        <DropdownMenu :modal="false">
                                            <DropdownMenuTrigger as-child>
                                                <Button data-item-dropdown class="absolute top-0 right-0 z-20" variant="ghost" size="icon">
                                                    <MoreHorizontal class="w-4 h-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent>
                                                <template v-if="canAddItem && field.sortable">
                                                    <DropdownMenuItem @click="onInsert(index)">
                                                        {{ __('sharp::form.list.insert_above_button') }}
                                                    </DropdownMenuItem>
                                                </template>
                                                <template v-if="props.field.removable">
                                                    <DropdownMenuSeparator class="first:hidden" />
                                                    <DropdownMenuItem class="text-destructive" @click="onRemove(index)">
                                                        {{ __('sharp::form.list.remove_button') }}
                                                    </DropdownMenuItem>
                                                </template>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </template>
                                </div>

                                <template v-if="field.sortable">
                                    <div class="z-10 absolute flex items-center justify-center right-0 top-1/2 translate-x-1/2 -translate-y-1/2 h-4 w-3 rounded-sm border bg-border duration-300 transition-opacity cursor-grab group-[&:has(.sortable-ghost)]/list:opacity-0 group-[&:has(.sortable-ghost)]/list:transition-none hover:bg-foreground hover:border-foreground hover:text-background group-hover:opacity-100"
                                        :class="reordering ? 'opacity-100 group-hover:bg-foreground group-hover:border-foreground group-hover:text-background' : ' opacity-0'"
                                        data-drag-handle
                                    >
                                        <div class="absolute -inset-3"></div>
                                        <GripVertical class="h-2.5 w-2.5" />
                                    </div>
                                </template>
                            </Card>
                        </template>
                    </TransitionGroup>
                </div>
            </template>


            <template v-if="!props.field.readOnly">
                <div class="relative grid grid-cols-1 gap-y-3"
                    @dragenter="($event as DragEvent).dataTransfer.types.includes('Files') && (bulkDroppingFile = true)"
                    @dragleave="(!$event.relatedTarget || !$el.contains($event.relatedTarget)) && (bulkDroppingFile = false)"
                >
                    <template v-if="canAddItem">
                        <div>
                            <Button
                                class="w-full gap-1"
                                :class="{ 'invisible': reordering }"
                                variant="secondary"
                                @click="onAdd"
                            >
                                <span aria-hidden="true">＋</span> {{ field.addText }}
                            </Button>
                        </div>
                    </template>
                    <template v-if="field.itemFields[field.bulkUploadField]?.type === 'upload' && canAddItem && currentBulkUploadLimit > 0">
                        <Card :class="{
                            'invisible': reordering,
                            'ring-2 ring-ring ring-offset-2': bulkDroppingFile
                        }">
                            <CardHeader :class="{ 'relative': !bulkDroppingFile }">
                                <div class="flex flex-wrap justify-center">
                                    <div class="text-sm"
                                        v-html='
                                            __(`sharp::form.list.bulk_upload.text`)
                                                .replace(
                                                    /\[(.+?)]\(.*?\)/,
                                                    `<button class="relative z-10 underline -mx-3 -my-2 ${buttonVariants({ variant: "link", size:"sm" })}" tabindex="-1">$1</button>`
                                                )
                                        '
                                        @click.prevent="($refs.bulkUploadInput as HTMLInputElement).click()"
                                    ></div>
                                    <div class="text-sm text-muted-foreground">
                                         ({{ __('sharp::form.list.bulk_upload.help_text', { limit: currentBulkUploadLimit }) }})
                                    </div>
                                </div>
                                <input
                                    class="absolute inset-0 opacity-0"
                                    type="file"
                                    :aria-label="__(`sharp::form.list.bulk_upload.text`)"
                                    :accept="(field.itemFields[field.bulkUploadField] as FormUploadFieldData).allowedExtensions?.join(',')"
                                    multiple
                                    @drop="bulkDroppingFile = false"
                                    @change="onBulkUploadInputChange"
                                    ref="bulkUploadInput"
                                >
                            </CardHeader>
                        </Card>
                    </template>
                </div>
            </template>

            <template v-if="field.readOnly && !value?.length">
                <div class="text-muted-foreground text-sm">
                    {{ __('sharp::form.list.empty') }}
                </div>
            </template>
        </div>
    </FormFieldLayout>
</template>
