<script setup lang="ts">
import { ChevronDown } from "lucide-vue-next";
import { cn } from "@/utils/cn";
</script>

<template>
    <ChevronDown :class="cn('size-4 opacity-50 ml-1.5 -mr-0.5', $attrs['class'])" />
</template>
