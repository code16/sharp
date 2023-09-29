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
    import Editor from "./fields/editor/EditorField.vue";
    import Geolocation from "./fields/geolocation/Geolocation.vue";
    import Html from "./fields/Html.vue";
    import List from "./fields/list/List.vue";
    import NumberInput from "./fields/Number.vue";
    import Select from "./fields/Select.vue";
    import TagInput from "./fields/Tags.vue";
    import Text from "./fields/Text.vue";
    import Textarea from "./fields/Textarea.vue";
    import Upload from "./fields/upload/Upload.vue";

    const props = defineProps<{
        field: FormFieldData,
        fieldLayout: LayoutFieldData,
        fieldErrorKey: string,
        value: FormFieldData['value'],
        locale?: string | null,
        root?: boolean
    }>();

    const emit = defineEmits(['input']);
    const form = inject('form') as Form;
    const id = computed(() => `form-field_${props.fieldErrorKey}`);

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
        if(options.error) {
            onError(options.error);
        } else {
            onClear();
        }
        emit('input', value, options);
    }
</script>

<template>
    <div class="SharpFieldContainer SharpForm__form-item"
        :class="[
            `SharpForm__form-item--type-${field.type}`
        ]"
        :style="field.extraStyle"
    >
        <div class="SharpForm__field-header" v-sticky="field.type === 'list'">
            <div class="row align-items-end">
                <div class="col d-flex">
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
                    <div class="col-auto">
                        <div class="SharpFieldLocaleSelect">
                            <div class="flex gap-1">
                                <template v-for="btnLocale in form.locales">
                                    <div class="col-auto d-flex">
                                        <button
                                            class="SharpFieldLocaleSelect__btn ml-2"
                                            :class="{
                                                'SharpFieldLocaleSelect__btn--active': btnLocale === locale,
                                                'SharpFieldLocaleSelect__btn--empty': form.fieldIsEmpty(field, value, btnLocale),
                                                'SharpFieldLocaleSelect__btn--error': form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale),
                                            }"
                                            @click="$emit('locale-change', field.key, btnLocale)"
                                        >
                                            {{ locale }}
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="SharpForm__field-content">
            <component
                :is="isCustomField(field.type) ? resolveCustomField(field.type) : components[field.type]"
                :id="id"
                :has-error="form.fieldHasError(field, fieldErrorKey)"
                v-bind="$props"
                @error="onError"
                @clear="onClear"
                @input="onInput"
            />
        </div>

        <template v-if="form.fieldHasError(field, fieldErrorKey)">
            <div class="text-sm text-red-700">
                <template v-if="form.fieldError(fieldErrorKey)">
                    {{ form.fieldError(fieldErrorKey) }}
                </template>
                <template v-else-if="'localized' in field && field.localized">
                    <template v-if="form.fieldError(`${fieldErrorKey}.${locale}`)">
                        {{ form.fieldError(`${fieldErrorKey}.${locale}`) }}
                    </template>
                    <template v-else>
                        {{ __('form.validation_error.localized', { locales: form.fieldLocalesContainingError(fieldErrorKey) }) }}
                    </template>
                </template>
            </div>
        </template>

        <template v-if="field.helpMessage">
            <div class="SharpForm__help-message form-text">{{ field.helpMessage }}</div>
        </template>
    </div>
</template>
