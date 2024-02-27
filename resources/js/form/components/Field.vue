<script setup lang="ts">
    import { FormFieldData, LayoutFieldData } from "@/types";
    import { computed, inject } from "vue";
    import type { Component } from 'vue';
    import { Form } from "../Form";
    import { vSticky } from "@/directives/sticky";
    import { __ } from "@/utils/i18n";
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
    import TagInput from "./fields/Tags.vue";
    import Text from "./fields/Text.vue";
    import Textarea from "./fields/Textarea.vue";
    import Upload from "./fields/upload/Upload.vue";
    import { useParentForm } from "../useParentForm";
    import { FormFieldProps } from "@/form/components/types";

    const props = defineProps<FormFieldProps & {
        field: FormFieldData,
        value?: FormFieldData['value'],
    }>();

    const emit = defineEmits(['input', 'locale-change']);
    const form = useParentForm();
    const id = computed(() => `form-field_${props.fieldErrorKey}`);

    const components: Record<FormFieldData['type'], Component> = {
        // 'autocomplete': Autocomplete,
        'check': Check,
        // 'date': DateInput,
        'editor': Editor,
        'geolocation': Geolocation,
        // 'html': Html,
        // 'list': List,
        // 'number': NumberInput,
        // 'select': Select,
        // 'tags': TagInput,
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
    <div class="SharpFieldContainer SharpForm__form-item"
        :class="[
            `SharpForm__form-item--type-${field.type}`
        ]"
        :style="field.extraStyle"
    >
        <div  v-sticky="field.type === 'list'">
            <div class="flex mb-1">
                <div class="flex-1">
                    <template v-if="field.label">
                        <label :for="id" class="SharpForm__label form-label">
                            {{ field.label }}
                        </label>
                    </template>
                    <template v-else>
                        <div class="form-label">&nbsp;</div>
                    </template>
                </div>
                <template v-if="'localized' in field && field.localized">
                    <div class="SharpFieldLocaleSelect">
                        <nav class="flex">
                            <template v-for="btnLocale in form.locales">
                                <button
                                    class="flex items-center rounded-md px-2 py-1 text-xs font-medium uppercase"
                                    :class="[
                                        btnLocale === locale ? 'bg-indigo-100 text-indigo-700' :
                                        form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale) ? 'text-red-700' :
                                        'text-gray-500 hover:text-gray-700',
                                        form.fieldIsEmpty(field, value, btnLocale) ? 'italic' : ''
                                    ]"
                                    :aria-current="btnLocale === locale ? 'true' : null"
                                    @click="$emit('locale-change', btnLocale)"
                                >
                                    {{ btnLocale }}
                                    <template v-if="form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale)">
                                        <svg class="ml-1 h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true">
                                            <circle cx="3" cy="3" r="3" />
                                        </svg>
                                    </template>
                                </button>
                            </template>
                        </nav>
                    </div>
                </template>
            </div>
        </div>

        <div class="SharpForm__field-content">
            <template v-if="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]">
                <component
                    :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
                    :id="id"
                    :has-error="form.fieldHasError(field, fieldErrorKey, locale)"
                    v-bind="$props"
                    @error="onError"
                    @clear="onClear"
                    @input="onInput"
                />
            </template>
            <template v-else>
                <div class="bg-black text-white px-4 py-2">
                    {{ field.type }}
                </div>
            </template>
        </div>

        <template v-if="form.fieldHasError(field, fieldErrorKey)">
            <div class="text-sm text-red-700 mt-1">
                <template v-if="form.fieldError(fieldErrorKey)">
                    {{ form.fieldError(fieldErrorKey) }}
                </template>
                <template v-else-if="'localized' in field && field.localized">
                    <template v-if="form.fieldError(`${fieldErrorKey}.${locale}`)">
                        {{ form.fieldError(`${fieldErrorKey}.${locale}`) }}
                    </template>
                    <template v-else>
                        {{ __('sharp::form.validation_error.localized', { locales: form.fieldLocalesContainingError(fieldErrorKey).map(l => l.toUpperCase()) }) }}
                    </template>
                </template>
            </div>
        </template>

        <template v-if="field.helpMessage">
            <div class="SharpForm__help-message form-text">{{ field.helpMessage }}</div>
        </template>
    </div>
</template>
