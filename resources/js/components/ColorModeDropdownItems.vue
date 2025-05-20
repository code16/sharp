<script setup lang="ts">
    import {
        DropdownMenuRadioGroup,
        DropdownMenuRadioItem,
    } from '@/components/ui/dropdown-menu'
    import { __ } from "@/utils/i18n";
    import { useColorMode } from "@/composables/useColorMode";

    const mode = useColorMode(true);
</script>

<template>
    <DropdownMenuRadioGroup v-model="mode">
        <DropdownMenuRadioItem value="light">
            {{ __('sharp::action_bar.color-mode-dropdown.light') }}
        </DropdownMenuRadioItem>
        <DropdownMenuRadioItem value="dark">
            {{ __('sharp::action_bar.color-mode-dropdown.dark') }}
        </DropdownMenuRadioItem>
        <DropdownMenuRadioItem value="auto">
            {{ __('sharp::action_bar.color-mode-dropdown.system') }}
        </DropdownMenuRadioItem>
    </DropdownMenuRadioGroup>
</template>
