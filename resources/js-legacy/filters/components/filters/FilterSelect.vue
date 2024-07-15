<script setup lang="ts">
    import { SelectFilterData } from "@/types";
    import { computed } from "vue";
    import {
        Combobox, ComboboxButton,
        ComboboxLabel, ComboboxOption, ComboboxOptions,
    } from "@headlessui/vue";
    import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
    import { TemplateRenderer } from "@/components";

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        disabled?: boolean,
    }>();

    const emit = defineEmits(['input'])

    const modelValue = computed<SelectFilterData['value']>({
        get() {
            if(props.filter.multiple && !props.value) {
                return [];
            }
            return props.value;
        },
        set(value) {
            emit('input', value);
        }
    });
</script>

<template>
    <div class="w-[10rem]">
        <Combobox
            v-model="modelValue"
            :multiple="filter.multiple"
            :disabled="disabled"
            :by="(a, b) => a == b"
            v-slot="{ open }"
        >
            <ComboboxLabel class="block text-sm font-medium leading-6 text-gray-900">
                {{ filter.label }}
            </ComboboxLabel>
            <div class="relative mt-2">
                <div class="relative form-input" :class="{'border-indigo-600 ring-1 ring-indigo-600': open}">
                    <template v-if="modelValue">
                        <template v-if="filter.multiple && Array.isArray(modelValue)">
                            <template v-for="(valueItem, i) in modelValue">
                                <span class="inline-flex items-center gap-x-0.5 rounded-md bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700">
                                    {{ filter.values.find(v => valueItem == v.id)?.label ?? valueItem }}
                                    <button type="button" class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-indigo-600/20"
                                        @click="modelValue = modelValue.filter(v => v !== valueItem)"
                                    >
                                        <span class="sr-only">Remove</span>
                                        <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-indigo-700/50 group-hover:stroke-indigo-700/75">
                                            <path d="M4 4l6 6m0-6l-6 6" />
                                        </svg>
                                        <span class="absolute -inset-1" />
                                    </button>
                                </span>
                            </template>
                            <template v-if="!modelValue?.length">
                                -
                            </template>
                        </template>
                        <template v-else>
                            <span class="block truncate">
                                <template v-if="filter.values.find(v => modelValue == v.id)">
                                    <TemplateRenderer
                                        :template="filter.template"
                                        :template-data="filter.values.find(v => modelValue == v.id)"
                                    />
                                </template>
                                <template v-else>
                                    {{ modelValue }}
                                </template>
                            </span>
                        </template>
                    </template>
                    <template v-else>
                        -
                    </template>
                    <ComboboxButton
                        class="absolute inset-0 w-full h-full flex justify-end items-center pr-2 cursor-text"
                        ref="button"
                    >
                        <ChevronUpDownIcon
                            class="h-5 w-5 text-gray-400"
                            aria-hidden="true"
                        />
                    </ComboboxButton>
                </div>
                <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0"
                    @enter="$emit('open')"
                    @leave="$emit('close')"
                >
                    <ComboboxOptions class="absolute z-30 mt-1 max-h-60 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none min-w-full max-w-xs sm:text-sm">
                        <template v-for="filterValue in filter.values">
                            <ComboboxOption as="template" :value="filterValue?.id" v-slot="{ active, selected }">
                                <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                    <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                                        {{ filterValue?.label ?? '-' }}
                                    </span>

                                    <template v-if="selected">
                                        <span :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                                            <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                        </span>
                                    </template>
                                </li>
                            </ComboboxOption>
                        </template>
                    </ComboboxOptions>
                </transition>
            </div>
        </Combobox>
    </div>



<!--        &lt;!&ndash; dropdown & search input &ndash;&gt;-->
<!--        <Autocomplete-->
<!--            class="SharpFilterSelect__select"-->
<!--            :value="autocompleteValue"-->
<!--            :local-values="values"-->
<!--            :search-keys="searchKeys"-->
<!--            :list-item-template="template"-->
<!--            :placeholder="__('sharp::entity_list.filter.search_placeholder')"-->
<!--            :multiple="multiple"-->
<!--            :hide-selected="multiple"-->
<!--            :allow-empty="!required"-->
<!--            :preserve-search="false"-->
<!--            :show-pointer="false"-->
<!--            :searchable="searchable"-->
<!--            :read-only="disabled"-->
<!--            nowrap-->
<!--            no-result-item-->
<!--            mode="local"-->
<!--            ref="autocomplete"-->
<!--            style="max-width: 0"-->
<!--            @multiselect-input="handleAutocompleteInput"-->
<!--            @close="close"-->
<!--        />-->

<!--        <FilterControl :label="label" @click="handleClicked">-->
<!--            &lt;!&ndash; value text & tags &ndash;&gt;-->
<!--            <Select-->
<!--                class="SharpFilterSelect__select form-select text-wrap"-->
<!--                :value="value"-->
<!--                :options="values"-->
<!--                :multiple="multiple"-->
<!--                :clearable="!required"-->
<!--                :read-only="disabled"-->
<!--                :inline="false"-->
<!--                placeholder=" "-->
<!--                ref="select"-->
<!--                @input="handleSelect"-->
<!--            >-->
<!--            </Select>-->
<!--        </FilterControl>-->
</template>
