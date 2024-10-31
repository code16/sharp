<script setup lang="ts">
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import {
        FormAutocompleteLocalFieldData,
        FormAutocompleteRemoteFieldData,
    } from "@/types";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { ref } from "vue";
    import { Button } from "@/components/ui/button";
    import { __ } from "@/utils/i18n";
    import { ChevronsUpDown, X } from 'lucide-vue-next';
    import { cn } from "@/utils/cn";
    import {
        Command,
        CommandEmpty,
        CommandGroup,
        CommandInput, CommandItem,
        CommandList,
        CommandSeparator
    } from "@/components/ui/command";
    import { route } from "@/utils/url";
    import { api } from "@/api/api";
    import { useParentForm } from "@/form/useParentForm";
    import debounce from "lodash/debounce";
    import { fuzzySearch } from "@/utils/search";

    const props = defineProps<FormFieldProps<FormAutocompleteLocalFieldData | FormAutocompleteRemoteFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormAutocompleteLocalFieldData | FormAutocompleteRemoteFieldData>>();
    const form = useParentForm();

    const open = ref(false);
    const searchTerm = ref('');
    const results = ref([]);

    const abort = new AbortController();
    const remoteSearch = debounce(async (query: string) => {
        results.value = await api.get(route('code16.sharp.api.form.autocomplete.index', {
            entityKey: form.entityKey,
            autocompleteFieldKey: props.field.key,
        }), {
            params: {
                endpoint: props.field.mode === 'remote' && props.field.remoteEndpoint,
                search: query,
            },
            signal: abort.signal,
        })
            .then(response => response.data.data);
    }, 300);

    function search(query: string) {
        if(props.field.mode === 'remote') {
            if(query.length >= props.field.searchMinChars) {
                remoteSearch(query);
            }
        } else {
            if(query.length > 0) {
                results.value = fuzzySearch(props.field.localValues, query, { searchKeys: props.field.searchKeys });
            }
        }
    }

    if(props.field.mode === 'local' && props.value) {
        const localValue = props.field.localValues
            .find(v => props.value[props.field.itemIdAttribute] == v[props.field.itemIdAttribute]);
        console.log(localValue);
        if(localValue) {
            emit('input', localValue, { force: true });
        }
    }
</script>

<template>
    <FormFieldLayout :field="props.field">
        <Popover v-model:open="open">
            <template v-if="props.value">
                <div class="relative border border-input flex items-center rounded-md min-h-10 text-sm px-3 py-2">
                    <div class="flex-1" v-html="props.value._htmlResult ?? props.value._html ?? props.value[props.field.itemIdAttribute]"></div>
                    <Button class="absolute right-0 top-1/2 -translate-y-1/2"  variant="ghost" size="icon" @click="$emit('input', null)">
                        <X class="size-4" />
                    </Button>
                </div>
            </template>
            <template v-else>
                <PopoverTrigger as-child>
                    <Button class="w-full justify-between" variant="outline">
                        {{ props.field.placeholder ?? __('sharp::form.multiselect.placeholder') }}
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
            </template>
            <PopoverContent :class="cn('p-0 w-[--radix-popover-trigger-width] min-w-[200px]')" align="start">
                <Command
                    v-model:searchTerm="searchTerm"
                    @update:modelValue="$emit('input', $event as any)"
                    @update:searchTerm="search($event)"
                >
                    <CommandInput :placeholder="__('sharp::form.multiselect.placeholder')" />
                    <CommandList>
                        <CommandEmpty>{{ __('sharp::form.autocomplete.no_results_text') }}</CommandEmpty>
                        <CommandGroup>
                            <template v-for="item in results" :key="item[props.field.itemIdAttribute]">
                                <CommandItem
                                    class="data-[state=checked]:bg-accent"
                                    :value="item"
                                    @select="$emit('input', item)"
                                >
                                    <div v-html="item._html"></div>
                                </CommandItem>
                            </template>
                        </CommandGroup>

<!--                        <template v-if="valuated">-->
<!--                            <div class="sticky -bottom-px border-b border-transparent bg-popover">-->
<!--                                <CommandSeparator />-->
<!--                                <CommandGroup>-->
<!--                                    <CommandItem-->
<!--                                        :value="{ label: __('sharp::filters.select.reset') }"-->
<!--                                        class="justify-center text-center"-->
<!--                                        @select="$emit('input', null); open = false"-->
<!--                                    >-->
<!--                                        {{ __('sharp::filters.select.reset') }}-->
<!--                                    </CommandItem>-->
<!--                                </CommandGroup>-->
<!--                            </div>-->
<!--                        </template>-->
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </FormFieldLayout>
</template>
