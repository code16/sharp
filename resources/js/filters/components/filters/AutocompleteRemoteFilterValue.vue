<script setup lang="ts">
    import { AutocompleteRemoteFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";
    import { computed } from "vue";

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
    const singleValue = computed(() => Array.isArray(props.value) ? null : props.value);
</script>

<template>
    <div class="flex gap-1" :class="{ 'flex-wrap': !inline }">
        <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-52 truncate">
            {{ singleValue?.label }}
        </Badge>
    </div>
</template>
