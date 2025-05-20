<script setup lang="ts">
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import {
        FormAutocompleteItemData,
        FormAutocompleteLocalFieldData,
        FormAutocompleteRemoteFieldData,
    } from "@/types";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { computed, ref } from "vue";
    import { Button } from "@/components/ui/button";
    import { __, trans_choice } from "@/utils/i18n";
    import { ChevronsUpDown, X, Check } from 'lucide-vue-next';
    import {
        Command,
        CommandEmpty,
        CommandGroup,
        CommandInput, CommandItem,
        CommandList,
    } from "@/components/ui/command";
    import { route } from "@/utils/url";
    import { api } from "@/api/api";
    import { useParentForm } from "@/form/useParentForm";
    import { isCancel } from "axios";
    import { ComboboxItemIndicator } from "reka-ui";
    import { useParentCommands } from "@/commands/useCommands";
    import { useIsInDialog } from "@/components/ui/dialog/Dialog.vue";
    import { useFullTextSearch } from "@/composables/useFullTextSearch";

    const props = defineProps<FormFieldProps<FormAutocompleteLocalFieldData | FormAutocompleteRemoteFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormAutocompleteLocalFieldData | FormAutocompleteRemoteFieldData>>();
    const form = useParentForm();

    const open = ref(false);
    const searchTerm = ref('');
    const results = ref<FormAutocompleteItemData[]>([]);
    const loading = ref(false);

    let abortController: AbortController | null = null;
    let timeout = null;
    let loadingTimeout = null;

    const parentCommands = useParentCommands();
    const isInDialog = useIsInDialog();
    const { fullTextSearch } = useFullTextSearch(
        () => props.field.mode === 'local' ? props.field.localValues : null,
        {
            id: props.field.itemIdAttribute,
            searchKeys: props.field.mode === 'local' ? props.field.searchKeys : [],
        }
    );

    function search(query: string, immediate?: boolean) {
        if(props.field.mode === 'remote') {
            const field = props.field as FormAutocompleteRemoteFieldData;
            clearTimeout(timeout);
            if(query.length >= field.searchMinChars) {
                if(!results.value.length) {
                    loading.value = true;
                }
                if(immediate) {
                    remoteSearch(query);
                } else {
                    timeout = setTimeout(() => remoteSearch(query), props.field.debounceDelay)
                }
            } else {
                clearTimeout(timeout);
                loading.value = false;
                results.value = [];
            }
        } else {
            results.value = !query.length
                ? props.field.localValues
                : fullTextSearch(query);
        }
    }

    async function remoteSearch(query: string) {
        const field = props.field as FormAutocompleteRemoteFieldData;
        clearTimeout(loadingTimeout);
        loadingTimeout = setTimeout(() => {
            loading.value = true;
        }, 200);
        abortController?.abort();
        abortController = new AbortController();
        results.value = await api.post(
            route('code16.sharp.api.form.autocomplete.index', {
                entityKey: form.entityKey,
                autocompleteFieldKey: props.parentField ? `${props.parentField.key}.${field.key}` : field.key,
                embed_key: form.embedKey,
                entity_list_command_key: parentCommands?.commandContainer === 'entityList' ? form.commandKey : null,
                show_command_key: parentCommands?.commandContainer === 'show' ? form.commandKey : null,
                instance_id: form.instanceId,
                endpoint: field.remoteEndpoint,
                search: query,
            }), {
                formData: field.callbackLinkedFields
                    ? Object.fromEntries(
                        Object.entries(props.parentData).filter(([fieldKey]) => field.callbackLinkedFields.includes(fieldKey))
                    )
                    : null,
            }, {
                signal: abortController.signal,
            }
        )
            .then(response => {
                clearTimeout(loadingTimeout);
                loading.value = false;
                return response;
            }, (e) => {
                if(isCancel(e)) {
                    clearTimeout(loadingTimeout);
                }
                return Promise.reject(e);
            })
            .then(response => response.data.data)
        ;
    }

    let hasTyped = false;

    function onSearchInput(query: string) {
        if(!query.length && !hasTyped) {
            return;
        }
        hasTyped = true;
        search(query);
    }

    function onOpen() {
        if(!searchTerm.value && (props.field.mode === 'local' || props.field.searchMinChars === 0)) {
            search('', true);
        }
    }

    function onSelect(id: string | number) {
        emit('input', results.value.find(r => String(r[props.field.itemIdAttribute]) === String(id)));
        open.value = false;
    }

    const currentLocalValue = computed(() => {
        return props.field.mode === 'local' && props.value
            ? props.field.localValues?.find(v => String(props.value[props.field.itemIdAttribute]) === String(v[props.field.itemIdAttribute]))
            : null;
    })
</script>

<template>
    <FormFieldLayout v-bind="props" field-group v-slot="{ ariaLabelledBy, ariaDescribedBy }">
        <Popover v-model:open="open" @update:open="$event ? onOpen() : null">
            <template v-if="props.value">
                <div class="relative">
                    <PopoverTrigger as-child>
                        <div class="relative border border-input flex items-center rounded-md min-h-10 text-sm px-3 py-2 pr-10 bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 aria-disabled:pointer-events-none aria-disabled:opacity-50"
                            role="combobox"
                            aria-autocomplete="none"
                            tabindex="0"
                            :aria-labelledby="ariaLabelledBy"
                            :aria-describedby="ariaDescribedBy"
                            :aria-disabled="props.field.readOnly"
                        >
                            <div class="flex-1"
                                v-html="currentLocalValue
                                    ? currentLocalValue._htmlResult ?? currentLocalValue._html
                                    : props.value._htmlResult ?? props.value._html ?? props.value[props.field.itemIdAttribute]
                                "
                            ></div>
                        </div>
                    </PopoverTrigger>
                    <Button class="absolute right-0 h-9.5 top-1/2 -translate-y-1/2 opacity-50 hover:opacity-100"
                        :disabled="props.field.readOnly"
                        variant="ghost"
                        size="icon"
                        :aria-label="__('sharp::form.select.clear')"
                        @click="$emit('input', null)"
                    >
                        <X class="size-4" aria-hidden="true" />
                    </Button>
                </div>
            </template>
            <template v-else>
                <PopoverTrigger as-child>
                    <Button
                        class="w-full justify-between text-muted-foreground px-3"
                        variant="outline"
                        role="combobox"
                        aria-autocomplete="none"
                        :disabled="props.field.readOnly"
                        :aria-labelledby="ariaLabelledBy"
                        :aria-describedby="ariaDescribedBy"
                    >
                        {{ props.field.placeholder ?? __('sharp::form.autocomplete.placeholder') }}
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50 text-foreground" />
                    </Button>
                </PopoverTrigger>
            </template>

            <PopoverContent
                class="p-0 w-(--reka-popover-trigger-width) min-w-[200px]"
                align="start"
                :avoid-collisions="false"
            >
                <Command
                    :class="isInDialog ? 'max-h-(--reka-popper-available-height)' : ''"
                    :model-value="value?.[props.field.itemIdAttribute]"
                    :reset-search-term-on-blur="false"
                    ignore-filter
                    @update:model-value="onSelect($event as any)"
                >
                    <CommandInput
                        v-model="searchTerm"
                        @update:model-value="onSearchInput"
                        :display-value="() => searchTerm"
                        :placeholder="props.value ? props.field.placeholder ?? __('sharp::form.autocomplete.placeholder') : null"
                    />
                    <CommandList>
                        <template v-if="loading">
                            <div class="py-6 px-4 text-center text-sm">
                                {{ __('sharp::form.autocomplete.loading') }}
                            </div>
                        </template>
                        <template v-else-if="!results?.length && props.field.mode === 'remote' && searchTerm.length < props.field.searchMinChars">
                            <div class="py-6 px-4 text-center text-sm">
                                {{ trans_choice('sharp::form.autocomplete.query_too_short', props.field.searchMinChars, { min_chars: props.field.searchMinChars }) }}
                            </div>
                        </template>
                        <template v-else>
                            <CommandEmpty>
                                {{ __('sharp::form.autocomplete.no_results_text') }}
                            </CommandEmpty>
                            <CommandGroup v-show="results.length">
                                <template v-for="item in results" :key="item[props.field.itemIdAttribute]">
                                    <CommandItem
                                        class="group/item"
                                        :value="item[props.field.itemIdAttribute]"
                                    >
                                        <div class="size-4">
                                            <ComboboxItemIndicator as-child>
                                                <Check class="size-4" />
                                            </ComboboxItemIndicator>
                                        </div>

                                        <div v-html="item._html"></div>
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </template>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </FormFieldLayout>
</template>
