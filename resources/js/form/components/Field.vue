<script setup lang="ts">
    import { FormFieldData, LayoutFieldData } from "@/types";
    import type { Component } from 'vue';
    import { isCustomField, resolveCustomField } from "@/utils/fields";
    import Autocomplete from "./fields/Autocomplete.vue";
    import Check from "./fields/Check.vue";
    import DateInput from "./fields/date/Date.vue";
    import Editor from "./fields/editor/Editor.vue";
    import Geolocation from "./fields/geolocation/Geolocation.vue";
    import Html from "./fields/Html.vue";
    import List from "./fields/list/List.vue";
    import NumberInput from "./fields/Number.vue";
    import Select from "./fields/Select.vue";
    import Tags from "./fields/Tags.vue";
    import Text from "./fields/Text.vue";
    import Textarea from "./fields/Textarea.vue";
    import Upload from "./fields/upload/Upload.vue";
    import { useParentForm } from "../useParentForm";

    import { FormFieldProps } from "@/form/types";

    const props = defineProps<FormFieldProps>();
    const emit = defineEmits(['input', 'locale-change']);
    const form = useParentForm();

    const components: Record<FormFieldData['type'], Component> = {
        'autocomplete': Autocomplete,
        'check': Check,
        // 'date': DateInput,
        'editor': Editor,
        'geolocation': Geolocation,
        'html': Html,
        'list': List,
        // 'number': NumberInput,
        'select': Select,
        'tags': Tags,
        'text': Text,
        'textarea': Textarea,
        'upload': Upload
    };

    function onError(error: string) {
        form.setError(props.fieldErrorKey, error);
        if('localized' in props.field && props.field.localized) {
            form.setError(`${props.fieldErrorKey}.${props.locale}`, error);
        }
    }

    function onClear() {
        form.clearError(props.fieldErrorKey);
        if('localized' in props.field && props.field.localized) {
            form.clearError(`${props.fieldErrorKey}.${props.locale}`);
        }
    }

    function onInput(value: FormFieldData['value'], options?: { force?: boolean, error?: string }) {
        if(props.field.readOnly && !options?.force) {
            return;
        }
        if(options?.error) {
            onError(options.error);
        } else {
            onClear();
        }
        emit('input', value, options);
    }

    if('localized' in props.field && props.field.localized) {
        emit('locale-change', props.locale);
    }
</script>

<template>
    <template v-if="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]">
        <component
            :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
            v-bind="$props"
            :has-error="form.fieldHasError(field, fieldErrorKey, locale)"
            @error="onError"
            @clear="onClear"
            @input="onInput"
            @locale-change="$emit('locale-change', $event)"
        />
    </template>
    <template v-else>
        <div class="bg-black text-white px-4 py-2">
            {{ field.type }}
        </div>
    </template>
</template>
