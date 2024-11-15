<script setup lang="ts">
    import { Label } from "@/components/ui/label";
    import { ShowFieldProps } from "@/show/types";
    import { ShowFieldData } from "@/types";

    const props = defineProps<ShowFieldProps<ShowFieldData, any>>();
</script>

<template>
    <div class="grid grid-cols-1 gap-2.5">
        <template v-if="'label' in props.field && !props.hideLabel">
            <Label>
                {{ props.field.label }}
            </Label>
        </template>

        <div>
            <slot />
        </div>
    </div>
</template>
