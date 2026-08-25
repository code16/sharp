<script setup lang="ts">
    import { computed, ref, watch } from "vue";
    import { X } from "lucide-vue-next";
    import { AutocompleteRemoteFilterData } from "@/types";
    import { FilterEmits, FilterProps } from "@/filters/types";
    import { useRemoteAutocomplete } from "@/composables/useRemoteAutocomplete";
    import { api } from "@/api/api";
    import { route } from "@/utils/url";
    import { __, trans_choice } from "@/utils/i18n";
    import { cn } from "@/utils/cn";
    import { Label } from "@/components/ui/label";
    import { Separator } from "@/components/ui/separator";
    import { Badge } from "@/components/ui/badge";
    import { Button } from "@/components/ui/button";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import SelectButton from "@/filters/components/filters/SelectButton.vue";
    import {
        Command,
        CommandEmpty,
        CommandGroup,
        CommandInput,
        CommandItem,
        CommandList,
        CommandSeparator,
    } from "@/components/ui/command";

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
    const selectedSummary = computed(() => {
        if (selectedValues.value.length === 1) {
            return selectedValues.value[0].label;
        }
        if (selectedValues.value.length > 1) {
            return trans_choice(
                'sharp::filters.select.label.selected',
                selectedValues.value.length,
                { count: selectedValues.value.length },
            );
        }
        return null;
    });

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

    function onRemove(option: Option) {
        updateValue(selectedValues.value.filter(selected => String(selected.id) !== String(option.id)));
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

        <Popover v-model:open="open" :modal="!inline" @update:open="$event ? onOpen() : null">
            <PopoverTrigger as-child>
                <SelectButton v-bind="props">
                    <template v-if="inline">
                        <template v-if="selectedSummary">
                            <Separator orientation="vertical" class="h-4" />
                            <Badge variant="secondary" class="block max-w-52 truncate rounded-sm px-1 font-normal">
                                {{ selectedSummary }}
                            </Badge>
                        </template>
                    </template>
                    <template v-else>
                        <Badge
                            v-if="selectedSummary"
                            variant="secondary"
                            class="block max-w-[calc(100%-1.5rem)] truncate rounded-sm px-1 font-normal"
                        >
                            {{ selectedSummary }}
                        </Badge>
                        <span v-else class="truncate text-muted-foreground">
                            {{ __('sharp::form.autocomplete.placeholder') }}
                        </span>
                    </template>
                </SelectButton>
            </PopoverTrigger>

            <PopoverContent
                :class="cn('p-0 w-auto min-w-[200px]', !inline ? 'w-(--reka-popover-trigger-width)' : '')"
                align="start"
            >
                <Command
                    :model-value="selectedValues"
                    by="id"
                    ignore-filter
                    multiple
                    highlight-on-hover
                    :reset-search-term-on-select="false"
                >
                    <CommandInput
                        class="field-sizing-content"
                        :model-value="searchTerm"
                        :placeholder="
                            filter.searchMinChars > 1
                                ? trans_choice('sharp::form.autocomplete.query_too_short', filter.searchMinChars, { min_chars: filter.searchMinChars })
                                : __('sharp::form.autocomplete.placeholder')
                        "
                        @update:model-value="onSearchInput"
                    />

                    <div
                        v-if="selectedValues.length"
                        class="flex max-w-full gap-1 overflow-x-auto border-b p-2"
                    >
                        <Badge
                            v-for="option in selectedValues"
                            :key="option.id"
                            variant="secondary"
                            class="max-w-52 shrink-0 gap-1 rounded-sm px-1 font-normal"
                        >
                            <span class="truncate">{{ option.label }}</span>
                            <button
                                type="button"
                                class="rounded-sm opacity-70 outline-none hover:opacity-100 focus-visible:ring-2 focus-visible:ring-ring"
                                :aria-label="__('sharp::form.tags.tag_delete_button.aria_label', { option_label: option.label })"
                                @mousedown.prevent
                                @click.stop="onRemove(option)"
                            >
                                <X class="size-3" />
                            </button>
                        </Badge>
                    </div>

                    <CommandList class="scroll-pb-12">
                        <template v-if="loading">
                            <div class="px-4 py-6 text-center text-sm">
                                {{ __('sharp::form.autocomplete.loading') }}
                            </div>
                        </template>
                        <template v-else-if="!availableResults.length && searchTerm.length < filter.searchMinChars">
                            <div class="px-4 py-6 text-center text-sm">
                                {{ trans_choice('sharp::form.autocomplete.query_too_short', filter.searchMinChars, { min_chars: filter.searchMinChars }) }}
                            </div>
                        </template>
                        <template v-else>
                            <CommandEmpty>
                                {{ __('sharp::form.autocomplete.no_results_text') }}
                            </CommandEmpty>
                            <CommandGroup v-if="availableResults.length">
                                <CommandItem
                                    v-for="option in availableResults"
                                    :key="option.id"
                                    :value="option"
                                    @select.prevent="onSelect(option)"
                                >
                                    <div class="max-w-80 line-clamp-2" v-html="option.label"></div>
                                </CommandItem>
                            </CommandGroup>
                        </template>

                        <template v-if="selectedValues.length">
                            <div class="sticky -bottom-px rounded-b-md border-b border-transparent bg-popover">
                                <CommandSeparator />
                                <div class="p-1">
                                    <Button class="h-8 w-full" variant="ghost" @click="updateValue([])">
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
