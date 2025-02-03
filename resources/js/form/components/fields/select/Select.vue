<script setup lang="ts">
    import { FormFieldEmitInputOptions, FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";
    import { nextTick, watch } from "vue";
    import DropdownSelect from "./DropdownSelect.vue";
    import Checkboxes from "./Checkboxes.vue";
    import Radios from "./Radios.vue";

    const props = defineProps<FormFieldProps<FormSelectFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();

    function onInput(value: FormSelectFieldData['value'], options?: FormFieldEmitInputOptions) {
        emit('input', value, { error: options?.error });
    }

    async function setDefaultValue() {
        if(!props.field.clearable
            && !props.field.multiple
            && props.value == null
            && props.field.options.length > 0
        ) {
            if(props.field.dynamicAttributes?.length) { // has dynamic options
                await nextTick();
            }
            emit('input', props.field.options[0].id, { force:true });
        }
    }

    watch(() => props.field.options, () => {
        setDefaultValue();
    });

    setDefaultValue();
</script>

<template>
    <template v-if="field.display === 'list'">
        <template v-if="field.multiple">
            <Checkboxes
                v-bind="{ ...$props, ...$attrs }"
                :value="value as Array<number|string> | null"
                @input="onInput"
            />
        </template>
        <template v-else>
            <Radios
                v-bind="{ ...$props, ...$attrs }"
                :value="value as string|number|null"
                @input="onInput" />
        </template>
    </template>
    <template v-else>
        <DropdownSelect v-bind="props" @input="onInput" />
    </template>
</template>
