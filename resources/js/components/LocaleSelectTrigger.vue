<script setup lang="ts">
    import { Languages } from "lucide-vue-next";
    import { SelectTrigger, SelectValue } from "@/components/ui/select/index.js";
</script>

<template>
    <SelectTrigger class="w-auto h-8 gap-2">
        <Languages class="w-4 h-4" />
        <SelectValue placeholder="-" />
    </SelectTrigger>
</template>
