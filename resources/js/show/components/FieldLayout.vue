<script setup lang="ts">
    import { Label } from "@/components/ui/label";

    defineProps<{
        label?: string,
    }>();
</script>

<template>
    <div>
        <template v-if="label">
            <Label>
                {{ label }}
            </Label>
        </template>

        <div :class="[label ? 'mt-2' : '']">
            <slot />
        </div>
    </div>
</template>
