<script setup lang="ts">
    import { MenuItem } from "@headlessui/vue";

    defineProps<{
        active: boolean,
        disabled: boolean,
    }>();

    defineEmits(['click']);
</script>

<template>
    <MenuItem v-slot="{ active: itemActive }" :disabled="disabled">
        <button
            :class="[
                disabled ? 'text-gray-400' :
                active || itemActive ? 'bg-gray-100 text-gray-900' :
                'text-gray-700',
                'block w-full text-left px-4 py-2 text-sm'
            ]"
            @click="$emit('click', $event)"
        >
            <slot />
        </button>
    </MenuItem>
</template>
