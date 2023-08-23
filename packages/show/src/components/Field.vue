<script setup lang="ts">
    import { ShowFieldData, ShowLayoutFieldData } from "@/types";
    import type { Component } from "vue";
    import EntityList from "./fields/entity-list/EntityList.vue";
    import Text from "./fields/text/Text.vue";
    import Picture from "./fields/Picture.vue";
    import File from "./fields/File.vue";
    import List from "./fields/List.vue";
    import { isCustomField, resolveCustomField } from "@/utils/fields";

    withDefaults(defineProps<{
        field: ShowFieldData,
        value: ShowFieldData['value'],
        layout: ShowLayoutFieldData,
        locale: string,
        locales: Array<string>,
        collapsable: boolean,
        root: boolean,
    }>(), {
        root: true,
    });

    const components: Record<Exclude<ShowFieldData['type'], 'html'>, Component> ={
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
            :field="field"
            :field-config-identifier="mergedConfigIdentifier"
            :value="value"
            :layout="layout"
            :collapsable="collapsable"
            :root="root"
            :locale="locale"
            :locales="locales"
            @visible-change="handleVisiblityChanged"
        />
    </div>
</template>

<script>
    import { ConfigNode } from "sharp/mixins";

    export default {
        mixins: [ConfigNode],
    }
</script>
