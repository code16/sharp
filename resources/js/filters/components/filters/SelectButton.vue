<script setup lang="ts">
import { ChevronDown } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import { FilterProps } from "@/filters/types";
import { AutocompleteRemoteFilterData, SelectFilterData } from "@/types";

const props = defineProps<FilterProps<SelectFilterData | AutocompleteRemoteFilterData>>();
</script>

<template>
    <template v-if="inline">
        <Button
            class="relative text-left justify-start h-8 py-1.5 gap-2 transition-shadow data-[state=open]:shadow-md"
            variant="outline"
            size="sm"
            role="combobox"
            aria-autocomplete="none"
            :aria-label="filter.label"
            :disabled="disabled"
        >
            <span aria-hidden="true">
                {{ filter.label }}
            </span>
            <slot />
            <ChevronDown class="-mr-0.5 w-4 h-4 opacity-50 shrink-0" />
        </Button>
    </template>
    <template v-else>
        <Button
            class="mt-2 h-auto min-h-9 w-full text-left justify-start font-normal py-1.5 gap-2"
            variant="outline"
            size="sm"
            role="combobox"
            aria-autocomplete="none"
            :aria-label="filter.label"
            :disabled="disabled"
        >
            <slot />
            <ChevronDown class="ml-auto w-4 h-4 opacity-50 shrink-0" />
        </Button>
    </template>
</template>
