<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormTagsFieldData } from "@/types";
    import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemText, TagsInputItemDelete } from "@/components/ui/tags-input";
    import { ComboboxAnchor, ComboboxContent, ComboboxPortal, ComboboxRoot, ComboboxInput } from "radix-vue";
    import { computed, ref } from "vue";
    import { __ } from "@/utils/i18n";
    import { CommandGroup, CommandItem, CommandList, CommandSeparator } from "@/components/ui/command";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { fuzzySearch } from "@/utils/search";

    const props = defineProps<FormFieldProps<FormTagsFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTagsFieldData>>();

    const open = ref(false);
    const searchTerm = ref('');

    let itemKeyIndex = 0;
    const itemKey = Symbol('itemKey');
    function withItemKey(option: FormTagsFieldData['options'][0]) {
        return {
            ...option,
            [itemKey as any]: itemKeyIndex++,
        }
    }

    function onCreateClick() {
        emit('input', [
            ...(props.value ?? []),
            withItemKey({
                id: null,
                label: searchTerm.value,
            }),
        ]);
    }

    function onOptionClick(option: FormTagsFieldData['options'][0]) {
        emit('input', [
            ...(props.value ?? []),
            withItemKey(option),
        ]);
    }

    const filteredOptions = computed(() => {
        const unselectedOptions = props.field.options.filter(o => !props.value?.find(v => o.id != null && v.id != null && o.id === v.id));
        return searchTerm.value.length > 0
            ? fuzzySearch(unselectedOptions, searchTerm.value, { searchKeys: ['label'] })
            : unselectedOptions;
    });

    emit('input', props.value.map(option => withItemKey(option)));
</script>

<template>
    <FormFieldLayout v-bind="props" v-slot="{ id, ariaDescribedBy }">
        <ComboboxRoot
            class="flex-1"
            :model-value="props.value"
            v-model:open="open"
            v-model:search-term="searchTerm"
        >
            <ComboboxAnchor>
                <TagsInput
                    class="ring-offset-background has-[:focus-visible]:ring-ring has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-offset-2"
                    :model-value="props.value"
                    :display-value="(item: typeof props.value[0]) => item.label ?? item.id"
                    @click="open = true; $refs.input.$el.focus()"
                >
                    <template v-for="item in value" :key="item[itemKey]">
                        <TagsInputItem :value="item">
                            <TagsInputItemText />
                            <TagsInputItemDelete @click.stop />
                        </TagsInputItem>
                    </template>

                    <ComboboxInput :placeholder="props.field.placeholder ?? __('sharp::form.multiselect.placeholder')" as-child>
                        <TagsInputInput :id="id" :aria-describedby="ariaDescribedBy" class="flex-1 w-[10rem]" @keydown.enter.prevent ref="input" />
                    </ComboboxInput>
                </TagsInput>
            </ComboboxAnchor>
            <ComboboxPortal>
                <ComboboxContent>
                    <CommandList
                        position="popper"
                        :avoid-collisions="false"
                        class="z-50 w-[--radix-popper-anchor-width] rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                    >
                        <template v-if="searchTerm.length > 0 && props.field.creatable">
                            <CommandGroup>
                                <CommandItem :value="{ id: null, label: searchTerm }" @select.prevent="onCreateClick()" :key="searchTerm">
                                    {{ props.field.createText }} “{{ searchTerm }}”
                                </CommandItem>
                            </CommandGroup>
                            <CommandSeparator />
                        </template>
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
                    </CommandList>
                </ComboboxContent>
            </ComboboxPortal>
        </ComboboxRoot>
    </FormFieldLayout>
</template>
