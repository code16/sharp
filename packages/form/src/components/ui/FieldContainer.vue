<script setup lang="ts">
    import { FormFieldData } from "@/types";
    import { computed, inject, provide } from "vue";
    import Field from '../Field.vue';
    import { Form } from "../../Form";
    import { vSticky } from "@/directives/sticky";
    import { __ } from "@/utils/i18n";

    const props = defineProps<{
        field: FormFieldData,
        value: FormFieldData['value'],
        locale: string | null,
        fieldErrorKey: string,
    }>();

    const form = inject('form') as Form;
    const fieldId = computed(() => `form-field_${props.fieldErrorKey}`);

    function onError(error) {
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

    function onInput(value, { error = null } = {}) {
        if(error) {
            onError(error);
        } else {
            onClear();
        }
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
                        <label :for="fieldId" class="SharpForm__label form-label">
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
            <Field
                v-bind="{ ...$props, ...$attrs }"
                :id="fieldId"
                :has-error="form.fieldHasError(field, fieldErrorKey)"
                @error="onError"
                @clear="onClear"
                @input="onInput"
                @locale-change="$emit('locale-change', $event)"
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
