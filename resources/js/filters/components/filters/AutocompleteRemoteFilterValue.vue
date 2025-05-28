<script setup lang="ts">
    import { AutocompleteRemoteFilterData, SelectFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";
    import { trans_choice } from "@/utils/i18n";

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
</script>

<template>
    <div class="flex gap-1" :class="{ 'flex-wrap': !inline }">
        <template v-if="value.length > 2">
            <Badge variant="secondary" class="rounded-sm px-1 font-normal">
                {{ trans_choice('sharp::filters.select.label.selected', value.length, { count: value.length }) }}
            </Badge>
        </template>
        <template v-else>
            <template v-for="selectValue in value" :key="selectValue.id">
                <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-52 truncate">
                    {{ selectValue.label }}
                </Badge>
            </template>
        </template>
    </div>
</template>
