<script setup lang="ts">
    import { SelectFilterData } from "@/types";
    import { ChevronDown, PlusCircle } from "lucide-vue-next";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { Separator } from "@/components/ui/separator";
    import { Badge } from "@/components/ui/badge";
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
    import { ref } from "vue";
    import FilterSelectValue from "@/filters/components/filters/FilterSelectValue.vue";

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        valuated: boolean,
        disabled?: boolean,
        inline?: boolean,
    }>();

    const emit = defineEmits(['input']);
    const open = ref(false);

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
                <template v-if="inline">
                    <Button variant="outline" size="sm" class="h-8 border-dashed transition-shadow shadow-sm data-[state=open]:shadow-md" :disabled="disabled">
                        <PlusCircle class="mr-2 w-4 h-4 stroke-[1.25]" />
                        {{ filter.label }}
                        <template v-if="Array.isArray(value) ? value.length : value != null">
                            <Separator orientation="vertical" class="mx-2 h-4" />
                            <FilterSelectValue :filter="filter" :value="value" inline />
                        </template>
                    </Button>
                </template>
                <template v-else>
                    <Button
                        class="mt-2 w-full text-left justify-start font-normal h-auto min-h-9 py-1.5 gap-1"
                        variant="outline"
                        size="sm"
                        :disabled="disabled"
                    >
                        <template v-if="Array.isArray(value) ? value.length : value != null">
                            <FilterSelectValue :filter="filter" :value="value" />
                        </template>
                        <template v-else>
                            <span class="text-muted-foreground">
                                {{ __('sharp::form.multiselect.placeholder') }}
                            </span>
                        </template>
                        <ChevronDown class="w-4 h-4 opacity-50 shrink-0 ml-auto" />
                    </Button>
                </template>
            </PopoverTrigger>
            <PopoverContent :class="cn('p-0 w-auto min-w-[200px]', !inline ? 'w-[--reka-popover-trigger-width]' : '')" align="start">
                <Command>
                    <template v-if="filter.searchable">
                        <CommandInput :placeholder="__('sharp::form.multiselect.placeholder')" />
                    </template>
                    <CommandList>
                        <CommandEmpty>{{ __('sharp::form.autocomplete.no_results_text') }}</CommandEmpty>
                        <CommandGroup>
                            <template v-for="selectValue in filter.values" :key="selectValue.id">
                                <CommandItem
                                    :value="selectValue"
                                    @select="onSelect(selectValue)"
                                >
                                    <template v-if="filter.multiple">
                                        <Checkbox
                                            class="mr-2"
                                            :class="{ 'opacity-50': !isSelected(selectValue) }"
                                            :model-value="isSelected(selectValue)"
                                        />
                                    </template>
                                    <template v-if="!filter.multiple">
                                        <Check
                                            :class="cn(
                                              'h-4 w-4 mr-2',
                                              isSelected(selectValue) ? 'opacity-100' : 'opacity-0',
                                            )"
                                        />
                                    </template>
                                    <div class="max-w-80 line-clamp-2">{{ selectValue.label }}</div>
                                </CommandItem>
                            </template>
                        </CommandGroup>

                        <template v-if="valuated">
                            <div class="sticky -bottom-px border-b border-transparent bg-popover">
                                <CommandSeparator />
                                <CommandGroup>
                                    <CommandItem
                                        :value="{ label: __('sharp::filters.select.reset') }"
                                        class="justify-center text-center"
                                        @select="$emit('input', null); open = false"
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
