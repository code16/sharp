<script setup lang="ts">
    import { Languages } from "lucide-vue-next";
    import { SelectTrigger, SelectValue } from "@/components/ui/select/index.js";
    import { __ } from "@/utils/i18n";
</script>

<template>
    <SelectTrigger class="w-auto h-8" :aria-label="__('sharp::action_bar.locale_selector.label')">
        <Languages class="size-3 mr-2.5 opacity-50" />
        <SelectValue class="uppercase text-xs" placeholder="-" />
    </SelectTrigger>
</template>
