<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormTagsFieldData } from "@/types";
    import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemText, TagsInputItemDelete } from "@/components/ui/tags-input";
    import { ComboboxAnchor, ComboboxContent, ComboboxPortal, ComboboxRoot, ComboboxInput } from "reka-ui";
    import { computed, ref, useTemplateRef, watch, nextTick } from "vue";
    import { __ } from "@/utils/i18n";
    import { CommandEmpty, CommandGroup, CommandItem, CommandList, CommandSeparator } from "@/components/ui/command";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { useFullTextSearch } from "@/composables/useFullTextSearch";

    const props = defineProps<FormFieldProps<FormTagsFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTagsFieldData>>();

    const open = ref(false);
    const input = useTemplateRef<InstanceType<typeof TagsInputInput>>('input');
    const searchTerm = ref('');

    let itemKeyIndex = 0;
    const itemKey = Symbol('itemKey');
    function withItemKey(option: FormTagsFieldData['options'][0]) {
        return {
            ...option,
            [itemKey as any]: itemKeyIndex++,
        }
    }

    const canAdd = computed(() => {
        return !props.field.maxTagCount
            || (props.value ?? []).length < props.field.maxTagCount
    });
    const canCreate = computed(() => {
        return props.field.creatable
            && searchTerm.value.length > 0
            && !props.value?.find(v => v.label === searchTerm.value)
            && !props.field.options.find(o => o.label === searchTerm.value);
    });

    function onCreateClick() {
        if(canAdd.value) {
            emit('input', [
                ...(props.value ?? []),
                withItemKey({
                    id: null,
                    label: searchTerm.value,
                }),
            ]);
        }
    }

    function onOptionClick(option: FormTagsFieldData['options'][0]) {
        if(canAdd.value) {
            emit('input', [
                ...(props.value ?? []),
                withItemKey(option),
            ]);
        }
    }

    async function onDeleteClick(item: FormTagsFieldData['value'][0]) {
        emit('input', props.value.filter(i => i[itemKey] !== item[itemKey]));
        await nextTick();
        onInputClick();
    }

    async function onInputClick() {
        if(canAdd.value) {
            open.value = true;
            await nextTick();
            input.value.$el.focus();
        }
    }

    watch(canAdd, () => {
        if(!canAdd.value && open.value) {
            open.value = false;
        }
    });

    const { fullTextSearch } = useFullTextSearch(() => props.field.options, { id: 'id', searchKeys: ['label'] });
    const filteredOptions = computed(() => {
        const filtered = searchTerm.value.length > 0
            ? fullTextSearch(searchTerm.value)
            : props.field.options;
        return filtered
            .filter(o => !props.value?.find(v => o.id != null && v.id != null && o.id === v.id)); // show only unselected options
    });

    emit('input', props.value?.map(option => withItemKey(option)), { skipRefresh: true });
</script>

<template>
    <FormFieldLayout v-bind="props" field-group v-slot="{ id, ariaDescribedBy }">
        <ComboboxRoot
            class="flex-1"
            :model-value="props.value"
            v-model:open="open"
            ignore-filter
        >
            <ComboboxAnchor>
                <TagsInput
                    class="min-h-10 ring-offset-background data-disabled:pointer-events-none data-disabled:opacity-50 has-[:focus-visible]:ring-ring has-focus-visible:ring-2 has-focus-visible:ring-offset-2"
                    :model-value="props.value"
                    :display-value="(item: typeof props.value[0]) => item.label ?? item.id"
                    :disabled="props.field.readOnly"
                    :max="props.field.maxTagCount"
                    @click="onInputClick"
                >
                    <template v-for="item in value" :key="item[itemKey]">
                        <TagsInputItem :value="item">
                            <TagsInputItemText />
                            <TagsInputItemDelete
                                :aria-labelledby="null"
                                :aria-label="__('sharp::form.tags.tag_delete_button.aria_label', { option_label: item.label })"
                                @click.stop="onDeleteClick(item)"
                            />
                        </TagsInputItem>
                    </template>

                    <ComboboxInput v-model="searchTerm" :placeholder="props.field.placeholder ?? __('sharp::form.multiselect.placeholder')" as-child>
                        <TagsInputInput :id="id" :aria-describedby="ariaDescribedBy" :disabled="props.field.readOnly" class="flex-1 w-40" v-show="canAdd" autocomplete="off" @keydown.enter.prevent ref="input" />
                    </ComboboxInput>
                </TagsInput>
            </ComboboxAnchor>
            <ComboboxPortal>
                <CommandList
                    position="popper"
                    position-strategy="absolute"
                    :avoid-collisions="false"
                    class="z-50 w-(--reka-popper-anchor-width) rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                >
                    <CommandEmpty>
                        {{ __('sharp::form.autocomplete.no_results_text') }}
                    </CommandEmpty>
                    <template v-if="canCreate">
                        <CommandGroup>
                            <CommandItem :value="{ id: null, label: searchTerm }" @select.prevent="onCreateClick()" :key="searchTerm">
                                {{ props.field.createText }} “{{ searchTerm }}”
                            </CommandItem>
                        </CommandGroup>
                        <CommandSeparator class="last:hidden" />
                    </template>

                    <template v-if="filteredOptions.length">
                        <CommandGroup>
                            <template
                                v-for="option in filteredOptions"
                                :key="option.id"
                            >
                                <CommandItem
                                    :value="option"
                                    @select.prevent="onOptionClick(option)"
                                >
                                    {{ option.label }}
                                </CommandItem>
                            </template>
                        </CommandGroup>
                    </template>
                </CommandList>
            </ComboboxPortal>
        </ComboboxRoot>
    </FormFieldLayout>
</template>
