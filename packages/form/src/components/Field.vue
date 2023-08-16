<script setup lang="ts">
    import { log, isCustomField, resolveCustomField } from 'sharp';
    import Fields from './fields/index';
    import { computed, provide } from "vue";

    const props = defineProps({
        // todo extract to common field props (cf FieldContainer.vue)
        fieldKey: String,
        fieldType: String,
        fieldProps: Object,
        fieldLayout: Object,
        value: [String, Number, Boolean, Object, Array, Date],
        locale: [Array, String],
        uniqueIdentifier: String,
        fieldConfigIdentifier: String,
        updateData: Function,
        readOnly: Boolean,
        root: Boolean,
    });

    const emit = defineEmits(['input']);

    const component = computed(() => {
        if(isCustomField(props.fieldType)) {
            return resolveCustomField(props.fieldType);
        }
        return Fields[props.fieldType];
    });

    const onInput = (val, options={}) => {
        if(props.fieldProps.readOnly && !options.force) {
            log(`SharpField '${props.fieldKey}', can't update because is readOnly`);
            return;
        }

        props.updateData(props.fieldKey, val, { forced:options.force });
        emit('input', val, {
            force: options.force,
            error: options.error,
        });
    };
    // console.log(structuredClone(props.fieldProps));
    console.log(props.fieldProps);
</script>
<!--<script lang="ts">export default { inheritAttrs: false }</script>-->
<template>
    <component
        :is="component"
        v-bind="{
            ...fieldProps,
            fieldKey,
            fieldLayout,
            value,
            locale,
            uniqueIdentifier,
            fieldConfigIdentifier,
            root,
        }"
        @input="onInput"
    />
</template>
