<script setup lang="ts">
    import { computed, ref, watch } from "vue";
    import { AutocompleteRemoteFilterData } from "@/types";
    import { FilterEmits, FilterProps } from "@/filters/types";
    import { useRemoteAutocomplete } from "@/composables/useRemoteAutocomplete";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import { __, trans_choice } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import { Separator } from "@/components/ui/separator";
    import { Button } from "@/components/ui/button";
    import {
        TagsInput,
        TagsInputInput,
        TagsInputItem,
        TagsInputItemDelete,
        TagsInputItemText,
    } from "@/components/ui/tags-input";
    import {
        CommandEmpty,
        CommandGroup,
        CommandItem,
        CommandList,
        CommandSeparator,
    } from "@/components/ui/command";
    import { ComboboxAnchor, ComboboxInput, ComboboxPortal, ComboboxRoot } from "reka-ui";

    type Option = { id: string | number, label: string };

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData, Option[]>>();
    const emit = defineEmits<FilterEmits<AutocompleteRemoteFilterData, Option[]>>();

    const open = ref(false);
    const searchTerm = ref('');
    const selectedValues = ref<Option[]>(Array.isArray(props.value) ? [...props.value] : []);

    watch(() => props.value, value => {
        selectedValues.value = Array.isArray(value) ? [...value] : [];
    });

    const { results, loading, search } = useRemoteAutocomplete<Option[]>(
        ({ query, signal, onSuccess, onError }) =>
            api.post(
                route('code16.sharp.api.filters.autocomplete.index', {
                    entityKey: props.entityKey,
                    filterHandlerKey: props.filter.key,
                    query,
                }),
                {},
                { signal }
            )
                .then(onSuccess, onError)
                .then(response => response.data.data),
        {
            debounceDelay: props.filter.debounceDelay,
            minLength: props.filter.searchMinChars,
        }
    );

    const availableResults = computed(() => results.value.filter(result => !isSelected(result)));

    function isSelected(option: Option) {
        return selectedValues.value.some(selected => String(selected.id) === String(option.id));
    }

    function updateValue(value: Option[]) {
        selectedValues.value = value;
        emit('input', value);
    }

    function onSelect(option: Option) {
        if (!isSelected(option)) {
            updateValue([...selectedValues.value, option]);
        }
    }

    function onTagsInput(value: Option[]) {
        updateValue(value ?? []);
    }

    function onSearchInput(query: string) {
        if (!query.length && !searchTerm.value) {
            return;
        }
        searchTerm.value = query;
        search(query);
    }

    function onOpen() {
        if (!searchTerm.value && props.filter.searchMinChars === 0) {
            search('', true);
        }
    }
</script>

<template>
    <div>
        <Label v-if="!inline">
            {{ filter.label }}
        </Label>

        <ComboboxRoot
            v-model:open="open"
            :model-value="selectedValues"
            by="id"
            ignore-filter
            multiple
            open-on-click
            open-on-focus
            :reset-search-term-on-select="false"
            @update:open="$event ? onOpen() : null"
        >
            <ComboboxAnchor>
                <TagsInput
                    :class="inline ? 'min-h-8 py-1 px-2' : 'mt-2 min-h-9'"
                    :model-value="selectedValues"
                    :display-value="(option: Option) => option.label"
                    :disabled="disabled"
                    @update:model-value="onTagsInput($event as Option[])"
                    @click="open = true"
                >
                    <template v-if="inline">
                        <span class="px-1" aria-hidden="true">{{ filter.label }}</span>
                        <Separator orientation="vertical" class="h-4" />
                    </template>

                    <template v-for="option in selectedValues" :key="option.id">
                        <TagsInputItem :value="option">
                            <TagsInputItemText />
                            <TagsInputItemDelete
                                :aria-labelledby="undefined"
                                :aria-label="__('sharp::form.tags.tag_delete_button.aria_label', { option_label: option.label })"
                                @click.stop
                            />
                        </TagsInputItem>
                    </template>

                    <ComboboxInput
                        :model-value="searchTerm"
                        :placeholder="
                            filter.searchMinChars > 1
                                ? trans_choice('sharp::form.autocomplete.query_too_short', filter.searchMinChars, { min_chars: filter.searchMinChars })
                                : __('sharp::form.autocomplete.placeholder')
                        "
                        as-child
                        @update:model-value="onSearchInput"
                    >
                        <TagsInputInput
                            class="min-w-28"
                            :aria-label="filter.label ?? __('sharp::form.autocomplete.placeholder')"
                            :disabled="disabled"
                            autocomplete="off"
                            @keydown.enter.prevent
                        />
                    </ComboboxInput>
                </TagsInput>
            </ComboboxAnchor>

            <ComboboxPortal>
                <CommandList
                    position="popper"
                    position-strategy="absolute"
                    class="z-50 mt-2 w-(--reka-popper-anchor-width) min-w-[200px] rounded-md border bg-popover text-popover-foreground shadow-md outline-none"
                >
                    <template v-if="loading">
                        <div class="py-6 px-4 text-center text-sm">
                            {{ __('sharp::form.autocomplete.loading') }}
                        </div>
                    </template>
                    <template v-else-if="!availableResults.length && searchTerm.length < filter.searchMinChars">
                        <div class="py-6 px-4 text-center text-sm">
                            {{ trans_choice('sharp::form.autocomplete.query_too_short', filter.searchMinChars, { min_chars: filter.searchMinChars }) }}
                        </div>
                    </template>
                    <template v-else>
                        <CommandEmpty>
                            {{ __('sharp::form.autocomplete.no_results_text') }}
                        </CommandEmpty>
                        <CommandGroup v-if="availableResults.length">
                            <template v-for="option in availableResults" :key="option.id">
                                <CommandItem :value="option" @select.prevent="onSelect(option)">
                                    <div class="max-w-80 line-clamp-2" v-html="option.label"></div>
                                </CommandItem>
                            </template>
                        </CommandGroup>
                    </template>

                    <template v-if="selectedValues.length">
                        <CommandSeparator />
                        <div class="p-1">
                            <Button class="w-full h-8" variant="ghost" @click="updateValue([])">
                                {{ __('sharp::filters.select.reset') }}
                            </Button>
                        </div>
                    </template>
                </CommandList>
            </ComboboxPortal>
        </ComboboxRoot>
    </div>
</template>
