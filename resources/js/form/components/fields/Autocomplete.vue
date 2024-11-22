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
    import { __, trans_choice } from "@/utils/i18n";
    import { ChevronsUpDown, X, Check } from 'lucide-vue-next';
    import { cn } from "@/utils/cn";
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
    import { fuzzySearch } from "@/utils/search";
    import {  isCancel } from "axios";
    import { ComboboxItemIndicator } from "reka-ui";
    import { useParentCommands } from "@/commands/useCommands";
    import { useIsInDialog } from "@/components/ui/dialog/Dialog.vue";

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

    function search(query: string, immediate?: boolean) {
        if(props.field.mode === 'remote') {
            const field = props.field as FormAutocompleteRemoteFieldData;
            clearTimeout(timeout);
            if(query.length >= field.searchMinChars) {
                if(!results.value.length) {
                    loading.value = true;
                }
                timeout = setTimeout(async () => {
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
                                    Object.entries(form.serializedData).filter(([fieldKey]) => field.callbackLinkedFields.includes(fieldKey))
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
                }, immediate ? 0 : props.field.debounceDelay)
            } else {
                clearTimeout(timeout);
                results.value = [];
            }
        } else {
            results.value = !query.length
                ? props.field.localValues
                : fuzzySearch(props.field.localValues, query, { searchKeys: props.field.searchKeys });
        }
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

    if(props.field.mode === 'local' && props.value) {
        const localValue = props.field.localValues
            .find(v => String(props.value[props.field.itemIdAttribute]) === String(v[props.field.itemIdAttribute]));
        if(localValue) {
            emit('input', localValue, { force: true });
        }
    }
</script>

<template>
    <FormFieldLayout v-bind="props">
        <Popover v-model:open="open" @update:open="$event ? onOpen() : null">
            <template v-if="props.value">
                <PopoverTrigger as-child>
                    <div class="relative border border-input flex items-center rounded-md min-h-10 text-sm px-3 py-2 pr-10 aria-disabled:pointer-events-none aria-disabled:opacity-50"
                        :aria-disabled="props.field.readOnly"
                    >
                        <div class="flex-1"
                            v-html="props.value._htmlResult ?? props.value._html ?? props.value[props.field.itemIdAttribute]"
                        ></div>
                        <Button class="absolute right-0 h-[2.375rem] top-1/2 -translate-y-1/2 opacity-50 hover:opacity-100"
                            :disabled="props.field.readOnly"
                            variant="ghost"
                            size="icon"
                            @click="$emit('input', null)"
                        >
                            <X class="size-4" />
                        </Button>
                    </div>
                </PopoverTrigger>
            </template>
            <template v-else>
                <PopoverTrigger as-child>
                    <Button class="w-full justify-between text-muted-foreground px-3" variant="outline" :disabled="props.field.readOnly">
                        {{ props.field.placeholder ?? __('sharp::form.autocomplete.placeholder') }}
                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50 text-foreground" />
                    </Button>
                </PopoverTrigger>
            </template>

            <PopoverContent
                class="p-0 w-[--reka-popover-trigger-width] min-w-[200px]"
                align="start"
                :avoid-collisions="false"
            >
                <Command
                    :class="isInDialog ? 'max-h-[--reka-popper-available-height]' : ''"
                    :model-value="value?.[props.field.itemIdAttribute]"
                    :reset-search-term-on-blur="false"
                    ignore-filter
                    @update:model-value="onSelect($event as any)"
                >
                    <CommandInput
                        v-model="searchTerm"
                        @update:model-value="search($event)"
                        :display-value="() => searchTerm"
                        :placeholder="props.value ? props.field.placeholder ?? __('sharp::form.autocomplete.placeholder') : null"
                    />
                    <CommandList>
                        <template v-if="loading">
                            <div class="py-6 text-center text-sm">
                                {{ __('sharp::form.autocomplete.loading') }}
                            </div>
                        </template>
                        <template v-else-if="!results.length && props.field.mode === 'remote' && searchTerm.length < props.field.searchMinChars">
                            <div class="py-6 text-center text-sm">
                                {{ trans_choice('sharp::form.autocomplete.query_too_short', props.field.searchMinChars, { min_chars: props.field.searchMinChars }) }}
                            </div>
                        </template>
                        <template v-else>
                            <CommandEmpty>
                                {{ __('sharp::form.autocomplete.no_results_text') }}
                            </CommandEmpty>
                            <CommandGroup>
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

<!--                                        <div class="hidden absolute top-0 -left-1 h-full border-l border-1 border-primary group-data-[state=checked]/item:block"></div>-->
                                        <div v-html="item._html"></div>

                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </template>

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
