<script setup lang="ts">
    import type { Component } from "vue";
    import Text from "./fields/text/Text.vue";
    import Picture from "./fields/Picture.vue";
    import File from "./fields/File.vue";
    import List from "./fields/List.vue";
    import { isCustomField, resolveCustomField } from "@/utils/fields";
    import { ShowFieldProps } from "../types";
    import { ShowFieldData } from "@/types";

    const props = defineProps<ShowFieldProps<ShowFieldData, any>>();

    const components: Record<Exclude<ShowFieldData['type'], 'html' | 'entityList'>, Component> = {
        'file': File,
        'list': List,
        'picture': Picture,
        'text': Text,
    }
</script>

<template>
    <component
        :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
        v-bind="props"
    />
</template>
