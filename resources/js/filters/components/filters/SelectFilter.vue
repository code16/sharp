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

    const props = defineProps<FilterProps<SelectFilterData>>();
    const emit = defineEmits<FilterEmits<SelectFilterData>>();
    const open = ref(false);

    const valuated = computed(() => Array.isArray(props.value) ? props.value.length : props.value != null);

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
                    <Button
                        class="relative text-left justify-start h-8 py-1.5 gap-2 transition-shadow data-[state=open]:shadow-md"
                        variant="outline"
                        size="sm"
                        :disabled="disabled"
                    >
                        {{ filter.label }}
                        <template v-if="valuated">
                            <Separator orientation="vertical" class="h-4" />
                            <SelectFilterValue v-bind="props" />
                        </template>
                        <ChevronDown class="-mr-0.5 w-4 h-4 opacity-50 shrink-0" />
                    </Button>
                </template>
                <template v-else>
                    <Button
                        class="mt-2 h-auto min-h-9 w-full text-left justify-start font-normal py-1.5 gap-2"
                        variant="outline"
                        size="sm"
                        :disabled="disabled"
                    >
                        <template v-if="valuated">
                            <SelectFilterValue v-bind="props" />
                        </template>
                        <template v-else>
                            <span class="text-muted-foreground">
                                {{ __('sharp::form.multiselect.placeholder') }}
                            </span>
                        </template>
                        <ChevronDown class="ml-auto w-4 h-4 opacity-50 shrink-0" />
                    </Button>
                </template>
            </PopoverTrigger>
            <PopoverContent :class="cn('p-0 w-auto min-w-[150px]', !inline ? 'w-[--reka-popover-trigger-width]' : '')" align="start">
                <Command
                    :multiple="props.filter.multiple"
                    highlight-on-hover
                >
                    <div v-show="filter.searchable">
                        <!-- v-show because otherwise highlight on hover does not work -->
                        <CommandInput :placeholder="__('sharp::form.multiselect.placeholder')" />
                    </div>

                    <CommandList>
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

                        <template v-if="valuated">
                            <div class="sticky -bottom-px border-b border-transparent bg-popover">
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
