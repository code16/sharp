<script setup lang="ts">
    import { Languages } from "lucide-vue-next";
    import { SelectTrigger, SelectValue } from "@/components/ui/select/index.js";
</script>

<template>
    <SelectTrigger class="w-auto h-8">
        <Languages class="size-3 mr-2.5 opacity-50" />
        <SelectValue class="uppercase text-xs" placeholder="-" />
    </SelectTrigger>
</template>
