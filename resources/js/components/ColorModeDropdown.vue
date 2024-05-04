<script setup lang="ts">
    import { Moon, Sun } from 'lucide-vue-next';
    import { Button } from '@/components/ui/button'
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuLabel,
        DropdownMenuTrigger
    } from '@/components/ui/dropdown-menu'
    import { __ } from "@/utils/i18n";
    import ColorModeDropdownItems from "@/components/ColorModeDropdownItems.vue";
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost">
                <Moon class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                <Sun class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>
                {{ __('sharp::action_bar.color-mode-dropdown.label') }}
            </DropdownMenuLabel>
            <ColorModeDropdownItems />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
