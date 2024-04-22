<script setup lang="ts">
    import { SelectFilterData } from "@/types";
    import { PlusCircle } from "lucide-vue-next";
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

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        disabled?: boolean,
    }>();

    const emit = defineEmits(['input']);

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
            emit('input', props.value == selectValue.id ? null : selectValue.id);
        }
    }
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" size="sm" class="h-8 border-dashed">
                <PlusCircle class="mr-2 h-4 w-4" />
                {{ filter.label }}
                <template v-if="Array.isArray(value) ? value.length : value != null">
                    <Separator orientation="vertical" class="mx-2 h-4" />
                    <Badge
                        variant="secondary"
                        class="rounded-sm px-1 font-normal lg:hidden"
                    >
                        {{ Array.isArray(value) ? value.length : 1 }}
                    </Badge>
                    <div class="hidden space-x-1 lg:flex">
                        <template v-if="Array.isArray(value)">
                            <template v-if="value.length > 2">
                                <Badge
                                    variant="secondary"
                                    class="rounded-sm px-1 font-normal"
                                >
                                    {{ value.length }} selected
                                </Badge>
                            </template>
                            <template v-else>
                                <template v-for="selectValue in filter.values.filter((v) => (value as Array<string | number>).some(vv => v.id == vv))" :key="selectValue.id">
                                    <Badge variant="secondary" class="rounded-sm px-1 font-normal">
                                        {{ selectValue.label }}
                                    </Badge>
                                </template>
                            </template>
                        </template>
                        <template v-else>
                            <Badge variant="secondary" class="rounded-sm px-1 font-normal">
                                {{ filter.values.find(selectValue => selectValue.id == value)?.label }}
                            </Badge>
                        </template>
                    </div>
                </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[200px] p-0" align="start">
            <Command
                :filter-function="(list: SelectFilterData['values'], term) => list.filter(i => i.label.toLowerCase()?.includes(term)) "
            >
                <template v-if="filter.searchable">
                    <CommandInput :placeholder="__('sharp::form.multiselect.placeholder')" />
                </template>
                <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup>
                        <template v-for="selectValue in filter.values" :key="selectValue.id">
                            <CommandItem
                                :value="selectValue"
                                @select="onSelect(selectValue)"
                            >
                                <Checkbox
                                    class="mr-2"
                                    :class="{ 'opacity-50': !isSelected(selectValue) }"
                                    :checked="isSelected(selectValue)"
                                />
                                <span>{{ selectValue.label }}</span>
                            </CommandItem>
                        </template>
                    </CommandGroup>

                    <template v-if="Array.isArray(value) ? value.length : value != null">
                        <CommandSeparator />
                        <CommandGroup>
                            <CommandItem
                                :value="{ label: 'Clear filters' }"
                                class="justify-center text-center"
                                @select="$emit('input', null)"
                            >
                                Clear filters
                            </CommandItem>
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
