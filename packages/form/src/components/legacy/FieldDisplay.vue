<script setup lang="ts">
    import { logError } from 'sharp';
    import { UnknownField } from "sharp/components";
    import FieldContainer from './ui/FieldContainer.vue';
    import { isLocalizableValueField } from "../util/locale";
    import { computeCondition } from '../util/conditional-display';
    import { isArray } from "axios/lib/utils";
    import { inject, computed, watch } from 'vue';

    const $form = inject('$form');

    const props = defineProps<{
        fieldKey: any,
        contextFields: any,
        contextData: any,
        errorIdentifier: any,
        updateVisibility: any,
        readOnly: any,
        locale: any,
    }>();

    function acceptCondition (fields, data, condition) {
        if(!condition)
            return true;

        return computeCondition(fields,data,condition);
    }

    const getValue = (form, field, value, locale) => {
        if(form.localized && field.localized && value && isLocalizableValueField(field)) {
            if(typeof value !== 'object' || isArray(value)) {
                logError(`Localized field '${field.key}' value must be a object, given :`, JSON.stringify(value));
                return value;
            }
            return value[locale];
        }

        return value;
    };

    const field = computed(() => props.contextFields[props.fieldKey]);
    const value = computed(() => props.contextData[props.fieldKey]);

    const isVisible = computed(() => acceptCondition(props.contextFields, props.contextData, field.value.conditionalDisplay));

    watch(isVisible, (isVisible) => props.updateVisibility(props.fieldKey, isVisible));
</script>

<template>
    <template v-if="!field">
        <UnknownField :name="fieldKey" />
    </template>
    <template v-else-if="isVisible">
        <FieldContainer
            :field-key="fieldKey"
            :field-props="{ ...field, readOnly: readOnly || field.readOnly }"
            :field-type="field.type"
            :value="getValue($form, field, value, locale)"
            :original-value="value"
            :label="field.label"
            :help-message="field.helpMessage"
            :error-identifier="errorIdentifier"
            :localized-error-identifier="field.localized ? `${errorIdentifier}.${props.locale}` : null"
            v-bind="$attrs"
        />
    </template>
</template>
