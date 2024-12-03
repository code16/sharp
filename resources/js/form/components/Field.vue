<script setup lang="ts">
    import { FormFieldData, LayoutFieldData } from "@/types";
    import type { Component } from 'vue';
    import { isCustomField, resolveCustomField } from "@/utils/fields";
    import Autocomplete from "./fields/Autocomplete.vue";
    import Check from "./fields/Check.vue";
    import Date from "./fields/Date.vue";
    import Editor from "./fields/editor/Editor.vue";
    import Geolocation from "./fields/geolocation/Geolocation.vue";
    import Html from "./fields/Html.vue";
    import List from "./fields/List.vue";
    import Number from "./fields/Number.vue";
    import Select from "./fields/select/Select.vue";
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
        'date': Date,
        'editor': Editor,
        'geolocation': Geolocation,
        'html': Html,
        'list': List,
        'number': Number,
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

    if(props.field && 'localized' in props.field && props.field.localized) {
        emit('locale-change', props.locale);
    }
</script>

<template>
    <template v-if="field && (isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type])">
        <component
            :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
            v-bind="props"
            :has-error="form.fieldHasError(field, fieldErrorKey, locale)"
            @error="onError"
            @clear="onClear"
            @input="onInput"
            @locale-change="$emit('locale-change', $event)"
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
