<script setup lang="ts">
    import { AutocompleteRemoteFilterData, SelectFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";
    import { trans_choice } from "@/utils/i18n";

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
</script>

<template>
    <div class="flex gap-1" :class="{ 'flex-wrap': !inline }">
        <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-52 truncate">
            {{ value.label }}
        </Badge>
    </div>
</template>
