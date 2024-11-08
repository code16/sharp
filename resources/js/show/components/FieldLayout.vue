<script setup lang="ts">
    import { Label } from "@/components/ui/label";

    defineProps<{
        label?: string,
    }>();
</script>

<template>
    <div class="grid grid-cols-1 gap-2.5">
        <template v-if="label">
            <Label>
                {{ label }}
            </Label>
        </template>

        <div>
            <slot />
        </div>
    </div>
</template>
