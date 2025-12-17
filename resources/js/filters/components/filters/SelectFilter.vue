<script setup lang="ts">
    import { SelectFilterData } from "@/types";
    import { ChevronDown } from "lucide-vue-next";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { Separator } from "@/components/ui/separator";
    import {
        Command,
        CommandEmpty,
        CommandGroup,
        CommandInput,
        CommandItem,
        CommandList, CommandSeparator
    } from "@/components/ui/command";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Button } from "@/components/ui/button";
    import { __ } from "@/utils/i18n";
    import { Check } from "lucide-vue-next";
    import { cn } from "@/utils/cn";
    import { Label } from "@/components/ui/label";
    import { computed, ref } from "vue";
    import SelectFilterValue from "@/filters/components/filters/SelectFilterValue.vue";
    import { FilterEmits, FilterProps } from "@/filters/types";
    import SelectButton from "@/filters/components/filters/SelectButton.vue";

    const props = defineProps<FilterProps<SelectFilterData>>();
    const emit = defineEmits<FilterEmits<SelectFilterData>>();
    const open = ref(false);

    const hasValue = computed(() => Array.isArray(props.value) ? props.value.length : props.value != null);

    function isSelected(selectValue: SelectFilterData['values'][0]) {
        return Array.isArray(props.value)
            ? !!props.value.find(v => selectValue.id == v)
            : props.value == selectValue.id;
    }

    function onSelect(selectValue: SelectFilterData['values'][0]) {
        if(props.filter.multiple) {
            const value = props.filter.values.filter(v => {
                const alreadySelected = (props.value as Array<number | string>)?.find(vv => v.id == vv);
                return v.id === selectValue.id ? !alreadySelected : alreadySelected;
            }).map(v => v.id);
            emit('input', value);
        } else {
            open.value = false;
            emit('input', props.value == selectValue.id ? null : selectValue.id);
        }
    }
</script>

<template>
    <div>
        <Label v-if="!inline">
            {{ filter.label }}
        </Label>
        <Popover v-model:open="open" :modal="!inline">
            <PopoverTrigger as-child>
                <SelectButton v-bind="props">
                    <template v-if="inline">
                        <template v-if="hasValue">
                            <Separator orientation="vertical" class="h-4" />
                            <SelectFilterValue v-bind="props" />
                        </template>
                    </template>
                    <template v-else>
                        <template v-if="hasValue">
                            <SelectFilterValue v-bind="props" />
                        </template>
                        <template v-else>
                            <span class="text-muted-foreground">
                                {{ __('sharp::form.multiselect.placeholder') }}
                            </span>
                        </template>
                    </template>
                </SelectButton>
            </PopoverTrigger>
            <PopoverContent :class="cn('p-0 w-auto min-w-[150px]', !inline ? 'w-(--reka-popover-trigger-width)' : '')" align="start">
                <Command
                    :multiple="props.filter.multiple"
                    highlight-on-hover
                >
                    <!-- v-show because otherwise highlight on hover does not work -->
                    <CommandInput v-show="filter.searchable" :placeholder="__('sharp::form.multiselect.placeholder')" />

                    <CommandList class="scroll-pb-12">
                        <CommandEmpty>{{ __('sharp::form.autocomplete.no_results_text') }}</CommandEmpty>
                        <CommandGroup>
                            <template v-for="selectValue in filter.values" :key="selectValue.id">
                                <CommandItem
                                    class="pr-6"
                                    :value="selectValue"
                                    :aria-selected="isSelected(selectValue)"
                                    @select="onSelect(selectValue)"
                                >
                                    <template v-if="filter.multiple">
                                        <Checkbox
                                            :class="{ 'opacity-50': !isSelected(selectValue) }"
                                            :model-value="isSelected(selectValue)"
                                        />
                                    </template>
                                    <template v-if="!filter.multiple">
                                        <Check
                                            :class="cn(
                                              'h-4 w-4',
                                              isSelected(selectValue) ? 'opacity-100' : 'opacity-0',
                                            )"
                                        />
                                    </template>
                                    <div class="max-w-80 line-clamp-2">{{ selectValue.label }}</div>
                                </CommandItem>
                            </template>
                        </CommandGroup>

                        <template v-if="props.valuated">
                            <div class="sticky -bottom-px border-b border-transparent rounded-b-md bg-popover">
                                <CommandSeparator />
                                <CommandGroup>
                                    <CommandItem
                                        :value="{ label: __('sharp::filters.select.reset') }"
                                        class="justify-center text-center font-medium"
                                        @select="emit('input', null); open = false"
                                    >
                                        {{ __('sharp::filters.select.reset') }}
                                    </CommandItem>
                                </CommandGroup>
                            </div>
                        </template>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </div>
</template>
