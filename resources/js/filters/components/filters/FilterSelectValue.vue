<script setup lang="ts">
    import { SelectFilterData } from "@/types";
    import { Badge } from "@/components/ui/badge";

    const props = defineProps<{
        value: SelectFilterData['value'],
        filter: Omit<SelectFilterData, 'value'>,
        inline?: boolean,
    }>();
</script>

<template>
    <div class="flex gap-1" :class="{ 'flex-wrap': !inline }">
        <template v-if="Array.isArray(value)">
            <template v-if="value.length > 2">
                <Badge variant="secondary" class="rounded-sm px-1 font-normal">
                    {{ value.length }} selected
                </Badge>
            </template>
            <template v-else>
                <template v-for="selectValue in filter.values.filter((v) => (value as Array<string | number>).some(vv => v.id == vv))" :key="selectValue.id">
                    <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-52 truncate">
                        {{ selectValue.label }}
                    </Badge>
                </template>
            </template>
        </template>
        <template v-else>
            <Badge variant="secondary" class="block rounded-sm px-1 font-normal max-w-44 truncate">
                {{ filter.values.find(selectValue => selectValue.id == value)?.label }}
            </Badge>
        </template>
    </div>
</template>
