<script setup lang="ts">
    import { log, isCustomField, resolveCustomField } from 'sharp';
    import Fields from './fields/index';
    import { computed, provide } from "vue";
    import { FormCustomFieldData, FormFieldData, LayoutFieldData, ShowFieldData } from "@/types";
    import Autocomplete from "./fields/Autocomplete.vue";
    import Text from "./fields/Text.vue";
    import Textarea from "./fields/Textarea.vue";
    import Editor from "./fields/editor/EditorField.vue";
    import NumberInput from "./fields/Number.vue";
    import Upload from "./fields/upload/Upload.vue";
    import TagInput from "./fields/Tags.vue";
    import DateInput from "./fields/date/Date.vue";
    import Check from "./fields/Check.vue";
    import List from "./fields/list/List.vue";
    import Select from "./fields/Select.vue";
    import Html from "./fields/Html.vue";
    import Geolocation from "./fields/geolocation/Geolocation.vue";
    import DateRange from "./fields/date-range/DateRange.vue";
    import { Component } from "vue/dist/vue";

    const props = defineProps<{
        field: FormFieldData,
        fieldLayout: LayoutFieldData,
        value: FormFieldData['value'],
        locale: string | null,
        uniqueIdentifier: String,
        updateData: Function,
        root: boolean
    }>();

    const emit = defineEmits(['input']);

    const components: Record<FormFieldData['type'], Component> = {
        'autocomplete': Autocomplete,
        'check': Check,
        'date': DateInput,
        'editor': Editor,
        'geolocation': Geolocation,
        'html': Html,
        'list': List,
        'number': NumberInput,
        'select': Select,
        'tags': TagInput,
        'text': Text,
        'textarea': Textarea,
        'upload': Upload
    };

    function onInput(val, options?: { force?: boolean, error?: string }) {
        if(props.field.readOnly && !options?.force) {
            log(`SharpField '${props.field.key}', can't update because is readOnly`);
            return;
        }

        emit('input', val, {
            force: options.force,
            error: options.error,
        });
    }
</script>

<template>
    <component
        :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
        v-bind="{
            field,
            value,
            locale,
            uniqueIdentifier,
            root
        }"
        @input="onInput"
    />
</template>
