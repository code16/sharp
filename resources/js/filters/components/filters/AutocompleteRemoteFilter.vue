<script setup lang="ts">
    import { AutocompleteRemoteFilterData } from "@/types";
    import { Label } from "@/components/ui/label";
    import { FilterEmits, FilterProps } from "@/filters/types";
    import { useRemoteAutocomplete } from "@/composables/useRemoteAutocomplete";
    import { api } from "@/api/api";
    import { __, trans_choice } from "@/utils/i18n";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { Separator } from "@/components/ui/separator";
    import SelectButton from "@/filters/components/filters/SelectButton.vue";
    import {
        Command,
        CommandEmpty,
        CommandGroup,
        CommandInput, CommandItem,
        CommandList,
        CommandSeparator
    } from "@/components/ui/command";
    import { Check } from "lucide-vue-next";
    import { cn } from "@/utils/cn";
    import { computed, ref, watch } from "vue";
    import AutocompleteRemoteFilterValue from "@/filters/components/filters/AutocompleteRemoteFilterValue.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";

    type Option = { id: string | number, label: string };

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
    const emit = defineEmits<FilterEmits<AutocompleteRemoteFilterData>>();
    const open = ref(false);

    // local copy so successive selections in a multiple filter don't wait for `value` to be updated by the parent
    const selectedValues = ref<Option[]>(toOptions(props.value));

    watch(() => props.value, value => {
        selectedValues.value = toOptions(value);
    });

    const searchTerm = ref('');
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
    )

    const displayedResults = computed(() => props.filter.multiple
        ? results.value.filter(result => !isSelected(result))
        : results.value
    );

    const showReset = computed(() => props.filter.multiple
        ? selectedValues.value.length > 0
        : props.valuated
    );

    function toOptions(value: AutocompleteRemoteFilterData['value']): Option[] {
        if(Array.isArray(value)) {
            return [...value];
        }
        return value ? [value] : [];
    }

    function isSelected(option: Option) {
        return selectedValues.value.some(selected => String(selected.id) === String(option.id));
    }

    function updateValue(value: Option[]) {
        selectedValues.value = value;
        emit('input', value);
    }

    function onSelect(option: Option, event: Event) {
        if(props.filter.multiple) {
            event.preventDefault();
            updateValue(isSelected(option)
                ? selectedValues.value.filter(selected => String(selected.id) !== String(option.id))
                : [...selectedValues.value, option]
            );
        } else {
            open.value = false;
            emit('input', isSelected(option) ? null : option);
        }
    }

    function onReset() {
        if(props.filter.multiple) {
            updateValue([]);
        } else {
            open.value = false;
            emit('input', null);
        }
    }

    function onSearchInput(query: string) {
        if(!query.length && !searchTerm.value) {
            return;
        }
        searchTerm.value = query;
        search(query);
    }

    function onOpen() {
        if(!searchTerm.value && props.filter.searchMinChars === 0) {
            search('', true);
        }
    }
</script>

<template>
    <div>
        <Label v-if="!inline">
            {{ filter.label }}
        </Label>
        <Popover v-model:open="open" :modal="!inline" @update:open="$event ? onOpen() : null">
            <PopoverTrigger as-child>
                <SelectButton v-bind="props">
                    <template v-if="inline">
                        <template v-if="selectedValues.length">
                            <Separator orientation="vertical" class="h-4" />
                            <AutocompleteRemoteFilterValue v-bind="props" :value="selectedValues" />
                        </template>
                    </template>
                    <template v-else>
                        <template v-if="selectedValues.length">
                            <AutocompleteRemoteFilterValue v-bind="props" :value="selectedValues" />
                        </template>
                        <template v-else>
                            <span class="text-muted-foreground">
                                {{ __('sharp::form.autocomplete.placeholder') }}
                            </span>
                        </template>
                    </template>
                </SelectButton>
            </PopoverTrigger>
            <PopoverContent :class="cn('p-0 w-auto min-w-[200px]', !inline ? 'w-(--reka-popover-trigger-width)' : '')" align="start">
                <Command
                    :model-value="filter.multiple ? selectedValues : undefined"
                    by="id"
                    :multiple="filter.multiple"
                    :reset-search-term-on-select="!filter.multiple"
                    ignore-filter
                    highlight-on-hover
                >
                    <CommandInput
                        class="field-sizing-content"
                        :model-value="searchTerm"
                        :placeholder="__('sharp::form.autocomplete.placeholder')"
                        @update:model-value="onSearchInput"
                    />

                    <CommandList class="scroll-pb-12">
                        <template v-if="filter.multiple && selectedValues.length">
                            <CommandGroup>
                                <template v-for="option in selectedValues" :key="option.id">
                                    <CommandItem
                                        class="pr-6"
                                        :value="option"
                                        aria-selected="true"
                                        @select="onSelect(option, $event)"
                                    >
                                        <Check class="h-4 w-4" />
                                        <div class="max-w-80 line-clamp-2" v-html="option.label"></div>
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                            <template v-if="displayedResults.length && !loading">
                                <CommandSeparator />
                            </template>
                        </template>
                        <template v-if="loading || !displayedResults.length">
                            <div class="sticky bg-popover" :class="showReset ? 'bottom-10' : 'bottom-0'">
                                <template v-if="filter.multiple && selectedValues.length">
                                    <CommandSeparator />
                                </template>
                                <div class="py-6 px-4 text-center text-sm">
                                    <template v-if="loading">
                                        {{ __('sharp::form.autocomplete.loading') }}
                                    </template>
                                    <template v-else-if="searchTerm.length < filter.searchMinChars">
                                        {{ trans_choice('sharp::form.autocomplete.query_too_short', filter.searchMinChars, { min_chars: filter.searchMinChars }) }}
                                    </template>
                                    <template v-else>
                                        {{ __('sharp::form.autocomplete.no_results_text') }}
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-else-if="displayedResults.length">
                            <CommandGroup>
                                <template v-for="option in displayedResults" :key="option.id">
                                    <CommandItem
                                        class="pr-6"
                                        :value="option"
                                        :aria-selected="isSelected(option)"
                                        @select="onSelect(option, $event)"
                                    >
                                        <Check
                                            :class="cn(
                                              'h-4 w-4',
                                              isSelected(option) ? 'opacity-100' : 'opacity-0',
                                            )"
                                        />
                                        <div class="max-w-80 line-clamp-2" v-html="option.label"></div>
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </template>
                        <template v-if="showReset">
                            <div class="sticky -bottom-px border-b border-transparent rounded-b-md bg-popover">
                                <CommandSeparator />
                                <div class="p-1">
                                    <Button class="w-full h-8" variant="ghost" @click="onReset">
                                        {{ __('sharp::filters.select.reset') }}
                                    </Button>
                                </div>
                            </div>
                        </template>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </div>
</template>
