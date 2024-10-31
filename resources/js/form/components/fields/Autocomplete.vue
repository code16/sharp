<script setup lang="ts">
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import {
        FormAutocompleteItemData,
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
    }, 200);

    function search(query: string) {
        if(props.field.mode === 'remote') {
            if(query.length >= props.field.searchMinChars) {
                remoteSearch(query);
            }
        } else {
            results.value = !query.length ? props.field.localValues : fuzzySearch(props.field.localValues, query, { searchKeys: props.field.searchKeys });
        }
    }

    function onOpen() {
        if((props.field.mode === 'remote' && props.field.searchMinChars === 0 || props.field.mode === 'local') && !searchTerm.value) {
            search('');
        }
    }

    function onSelect(result: FormAutocompleteItemData) {
        emit('input', result);
        open.value = false;
    }

    if(props.field.mode === 'local' && props.value) {
        const localValue = props.field.localValues
            .find(v => props.value[props.field.itemIdAttribute] == v[props.field.itemIdAttribute]);
        if(localValue) {
            emit('input', localValue, { force: true });
        }
    }
</script>

<template>
    <FormFieldLayout :field="props.field">
        <Popover v-model:open="open" @update:open="$event ? onOpen() : null">
            <template v-if="props.value">
                <PopoverTrigger as-child>
                    <div class="relative border border-input flex items-center rounded-md min-h-10 text-sm px-3 py-2">
                        <div class="flex-1" @click.stop v-html="props.value._htmlResult ?? props.value._html ?? props.value[props.field.itemIdAttribute]"></div>
                        <Button class="absolute right-0 h-[2.375rem] top-1/2 -translate-y-1/2"  variant="ghost" size="icon" @click="$emit('input', null)">
                            <X class="size-4 opacity-50" />
                        </Button>
                    </div>
                </PopoverTrigger>
            </template>
            <template v-else>
                <PopoverTrigger as-child>
                    <Button class="w-full justify-between text-muted-foreground px-3" variant="outline">
                        {{ props.field.placeholder ?? __('sharp::form.autocomplete.placeholder') }}
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50 text-foreground" />
                    </Button>
                </PopoverTrigger>
            </template>

            <PopoverContent :class="cn('p-0 w-[--radix-popover-trigger-width] min-w-[200px]')" align="start">
                <Command
                    v-model:searchTerm="searchTerm"
                    @update:modelValue="onSelect($event as any)"
                    @update:searchTerm="search($event)"
                >
                    <CommandInput  />
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
