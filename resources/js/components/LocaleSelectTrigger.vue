<script setup lang="ts">
    import { Languages } from "lucide-vue-next";
    import { SelectTrigger, SelectValue } from "@/components/ui/select/index.js";
    import { __ } from "@/utils/i18n";

    defineProps<{
        errorCount?: number,
    }>();
</script>

<template>
    <SelectTrigger class="w-auto gap-0 h-8" :aria-label="$attrs['aria-label'] ?? __('sharp::action_bar.locale_selector.label')">
        <Languages class="size-3 mr-2.5 opacity-50" />
        <SelectValue class="uppercase text-xs" placeholder="-" />
        <template v-if="errorCount">
            <div class="ml-1.5 -mr-0.5 grid place-content-center text-[.625rem]/2.5 px-1 h-3.5 min-w-3.5 bg-destructive rounded-full text-destructive-foreground font-semibold">
                {{ errorCount }}
            </div>
        </template>
    </SelectTrigger>
</template>
