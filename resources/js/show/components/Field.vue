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
    <template v-if="props.field && (isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type])">
        <component
            :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
            v-bind="props"
        />
    </template>
    <template v-else>
        <div class="bg-destructive text-destructive-foreground text-sm px-4 py-2">
            <template v-if="!props.field">
                Undefined field: <span class="font-mono">{{ props.fieldLayout.key }}</span>
            </template>
            <template v-else>
                Unknown field type: <div class="font-mono">{{ props.field.type }}</div>
            </template>
        </div>
    </template>
</template>
