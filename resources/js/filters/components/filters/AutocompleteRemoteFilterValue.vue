<script setup lang="ts">
    import { AutocompleteRemoteFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";
    import { FilterProps } from "@/filters/types";
    import { trans_choice } from "@/utils/i18n";
    import { computed } from "vue";

    const props = defineProps<FilterProps<AutocompleteRemoteFilterData>>();
    const values = computed(() => Array.isArray(props.value) ? props.value : props.value ? [props.value] : []);
</script>

<template>
    <div class="flex gap-1" :class="{ 'flex-wrap': !inline }">
        <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-52 truncate">
            <template v-if="values.length > 1">
                {{ trans_choice('sharp::filters.select.label.selected', values.length, { count: values.length }) }}
            </template>
            <template v-else>
                {{ values[0]?.label }}
            </template>
        </Badge>
    </div>
</template>
