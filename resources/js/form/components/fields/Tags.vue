<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormTagsFieldData } from "@/types";
    import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemText, TagsInputItemDelete } from "@/components/ui/tags-input";
    import { ComboboxAnchor, ComboboxContent, ComboboxPortal, ComboboxRoot, ComboboxInput } from "radix-vue";
    import { ref } from "vue";
    import { __ } from "@/utils/i18n";
    import { CommandEmpty, CommandGroup, CommandItem, CommandList, CommandSeparator } from "@/components/ui/command";
    import { SelectEvent } from "radix-vue/dist/Combobox/ComboboxItem";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormTagsFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTagsFieldData>>();

    const open = ref(false);
    const searchTerm = ref('');

    function onComboboxChange(value: typeof props.value) {
        // emit('input', value);
    }

    function onComboboxItemSelect(e: SelectEvent<typeof props.value[0]>) {
        emit('input', [
            ...(props.value ?? []),
            e.detail.value,
        ]);
    }
</script>

<template>
    <FormFieldLayout :field="props.field">
        <TagsInput class="px-0 gap-0" :model-value="props.value" :display-value="(item: typeof props.value[0]) => item.label">
            <div class="flex gap-2 flex-wrap items-center px-3">
                <template v-for="item in value">
                    <TagsInputItem :value="item">
                        <TagsInputItemText />
                        <TagsInputItemDelete />
                    </TagsInputItem>
                </template>
            </div>
            <ComboboxRoot
                class="w-full"
                :model-value="props.value"
                v-model:open="open"
                v-model:search-term="searchTerm"
                @update:model-value="(v: typeof props.value) => onComboboxChange(v)"
            >
                <ComboboxAnchor as-child>
                    <ComboboxInput :placeholder="props.field.placeholder ?? __('sharp::form.multiselect.placeholder')" as-child>
                        <TagsInputInput class="w-full px-3" :class="props.value.length > 0 ? 'mt-2' : ''" @keydown.enter.prevent />
                    </ComboboxInput>
                </ComboboxAnchor>
                <ComboboxPortal>
                    <ComboboxContent>
                        <CommandList
                            position="popper"
                            class="z-50 w-[--radix-popper-anchor-width] rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                        >
                            <CommandEmpty />

                            <template v-if="searchTerm?.length > 0 && props.field.creatable">
                                <CommandGroup>
                                    <CommandItem :value="{ label: searchTerm }" @select.prevent="onComboboxItemSelect">
                                        {{ props.field.createText }} “{{ searchTerm }}”
                                    </CommandItem>
                                </CommandGroup>
                                <CommandSeparator />
                            </template>
                            <CommandGroup>
                                <template v-for="option in props.field.options.filter(o => !props.value?.find(v => o.id === v.id))" :key="option.id">
                                    <CommandItem
                                        :value="option"
                                        @select.prevent="onComboboxItemSelect"
                                    >
                                        {{ option.label }}
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </CommandList>
                    </ComboboxContent>
                </ComboboxPortal>
            </ComboboxRoot>
        </TagsInput>
    </FormFieldLayout>
</template>
