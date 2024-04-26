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
    import { SelectTrigger } from "@/components/ui/select";
    import FilterControl from "@/filters/components/FilterControl.vue";
    import FilterControlButton from "@/filters/components/FilterControlButton.vue";
    import { PopoverAnchor } from "radix-vue";

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        inline?: boolean,
    }>();
</script>

<template>
    <div class="flex flex-wrap gap-1">
        <template v-if="Array.isArray(value)">
            <template v-if="value.length > 2">
                <Badge variant="secondary" class="rounded-sm px-1 font-normal">
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
