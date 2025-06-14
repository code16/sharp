<script setup lang="ts">
    import { AutocompleteRemoteFilterData } from "@/types";
    import { Checkbox } from "@/components/ui/checkbox";
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
    import { computed, ref } from "vue";
    import AutocompleteRemoteFilterValue from "@/filters/components/filters/AutocompleteRemoteFilterValue.vue";
    import { route } from "@/utils/url";
    import { Button } from "@/components/ui/button";

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
    const emit = defineEmits<FilterEmits<AutocompleteRemoteFilterData>>();
    const open = ref(false);

    const searchTerm = ref('');
    const { results, loading, search } = useRemoteAutocomplete<AutocompleteRemoteFilterData['value'][]>(
        ({ query, signal, onSuccess, onError }) =>
            api.post(
                route('code16.sharp.api.filters.autocomplete.index', {
                    entityKey: props.entityKey,
                    filterKey: props.filter.key,
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

    function isSelected(selectValue: AutocompleteRemoteFilterData['value']) {
        return Array.isArray(props.value)
            ? !!props.value.find(v => selectValue.id == v.id)
            : props.value?.id == selectValue.id;
    }

    function onSelect(selectValue: AutocompleteRemoteFilterData['value']) {
        // if(props.filter.multiple) {
        //     const value = Object.values({
        //         ...Object.fromEntries(
        //             Object.entries(props.value || []).map(([i,v]) => [v.id, v])
        //         ),
        //         [selectValue.id]: selectValue,
        //     });
        //     emit('input', value);
        // } else {
            open.value = false;
            emit('input', props.value?.id == selectValue.id ? null : selectValue);
        // }
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
                        <template v-if="props.value">
                            <Separator orientation="vertical" class="h-4" />
                            <AutocompleteRemoteFilterValue v-bind="props" />
                        </template>
                    </template>
                    <template v-else>
                        <template v-if="props.value">
                            <AutocompleteRemoteFilterValue v-bind="props" />
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
                <Command highlight-on-hover>
<!--                :multiple="props.filter.multiple" -->
                    <CommandInput
                        class="field-sizing-content"
                        :model-value="searchTerm"
                        :placeholder="
                            props.filter.searchMinChars > 1
                                ? trans_choice('sharp::form.autocomplete.query_too_short', props.filter.searchMinChars, { min_chars: props.filter.searchMinChars })
                                : __('sharp::form.autocomplete.placeholder')
                        "
                        @update:model-value="onSearchInput"
                    />

                    <CommandList class="scroll-pb-12">
                        <template v-if="loading">
                            <div class="py-6 px-4 text-center text-sm">
                                {{ __('sharp::form.autocomplete.loading') }}
                            </div>
                        </template>
<!--                        <template v-else-if="!results?.length && searchTerm.length < props.filter.searchMinChars">-->
<!--                            <div class="py-6 px-4 text-center text-sm">-->
<!--                                {{ trans_choice('sharp::form.autocomplete.query_too_short', props.filter.searchMinChars, { min_chars: props.filter.searchMinChars }) }}-->
<!--                            </div>-->
<!--                        </template>-->
                        <template v-else>
                            <CommandEmpty>{{ __('sharp::form.autocomplete.no_results_text') }}</CommandEmpty>
                            <template v-if="results.length">
                                <CommandGroup>
                                    <template v-for="selectValue in results" :key="selectValue.id">
                                        <CommandItem
                                            class="pr-6"
                                            :value="selectValue"
                                            :aria-selected="isSelected(selectValue)"
                                            @select="onSelect(selectValue)"
                                        >
<!--                                            <template v-if="filter.multiple">-->
<!--                                                <Checkbox-->
<!--                                                    :class="{ 'opacity-50': !isSelected(selectValue) }"-->
<!--                                                    :model-value="isSelected(selectValue)"-->
<!--                                                />-->
<!--                                            </template>-->
<!--                                            <template v-if="!filter.multiple">-->
                                                <Check
                                                    :class="cn(
                                                      'h-4 w-4',
                                                      isSelected(selectValue) ? 'opacity-100' : 'opacity-0',
                                                    )"
                                                />
<!--                                            </template>-->
                                            <div class="max-w-80 line-clamp-2" v-html="selectValue.label"></div>
                                        </CommandItem>
                                    </template>
                                </CommandGroup>
                            </template>
                        </template>
                        <template v-if="valuated">
                            <div class="sticky -bottom-px border-b border-transparent rounded-b-md bg-popover">
                                <template v-if="results.length || loading">
                                    <CommandSeparator />
                                </template>
                                <div class="p-1">
                                    <Button class="w-full h-8" variant="ghost" @click="emit('input', null); open = false">
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
