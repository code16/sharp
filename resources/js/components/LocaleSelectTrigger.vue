<script setup lang="ts">
    import { Languages } from "lucide-vue-next";
    import { SelectTrigger, SelectValue } from "@/components/ui/select/index.js";
    import { Button } from "@/components/ui/button";
    import { SelectIcon } from "reka-ui";
    import DropdownChevronDown from "@/components/ui/DropdownChevronDown.vue";
</script>

<template>
    <SelectTrigger as-child>
        <Button class="w-auto h-8" variant="outline">
            <Languages class="w-4 h-4 mr-2" />
            <SelectValue class="uppercase text-xs" placeholder="-" />
            <SelectIcon as-child>
                <DropdownChevronDown />
            </SelectIcon>
        </Button>
    </SelectTrigger>
</template>
