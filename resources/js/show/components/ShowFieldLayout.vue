<script setup lang="ts">
    import { Label } from "@/components/ui/label";
    import { ShowFieldProps } from "@/show/types";
    import { ShowFieldData } from "@/types";
    import { computed } from "vue";

    const props = defineProps<ShowFieldProps<ShowFieldData, any>>();
    const hasLabelRow = computed(() =>
        !props.hideLabel && (
            'label' in props.field && props.field.label
            || props.row?.length > 1
        )
    );
</script>

<template>
    <div class="grid grid-cols-1 gap-2.5" :class="hasLabelRow ? '@md/field-container:grid-rows-subgrid @md/field-container:row-span-2' : ''">
        <template v-if="hasLabelRow">
            <div class="flex" :class="!('label' in props.field && props.field.label) ? 'max-md:hidden' : ''">
                <template v-if="'label' in props.field">
                    <Label>
                        {{ props.field.label }}
                    </Label>
                </template>
            </div>
        </template>

        <div>
            <slot />
        </div>
    </div>
</template>
