<script setup lang="ts">
    import type { Component } from "vue";
    import EntityList from "./fields/entity-list/EntityList.vue";
    import Text from "./fields/text/Text.vue";
    import Picture from "./fields/Picture.vue";
    import File from "./fields/File.vue";
    import List from "./fields/List.vue";
    import { isCustomField, resolveCustomField } from "@/utils/fields";
    import { ShowFieldProps } from "../types";
    import { ShowFieldData } from "@/types";

    defineProps<ShowFieldProps & {
        field: ShowFieldData,
        value?: ShowFieldData['value'],
    }>();

    const components: Record<Exclude<ShowFieldData['type'], 'html'>, Component> = {
        'entityList': EntityList,
        'file': File,
        'list': List,
        'picture': Picture,
        'text': Text,
    }
</script>

<template>
    <div class="show-field" :class="`show-field--${field.type}`">
        <component
            :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
            v-bind="$props"
        />
    </div>
</template>
