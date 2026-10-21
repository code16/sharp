<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { FormFieldProps } from "@/form/types";
    import { Input } from "@/components/ui/input";

    const props = defineProps<FormFieldProps<FormTextFieldData> & {
        passwordVisible?: boolean,
    }>();
    const model = defineModel<string>();

    const input = ref();

    defineExpose({
        focus: () => input.value.$el.focus(),
    });

    defineOptions({
        inheritAttrs: false,
    });
</script>

<template>
    <Input
        :class="props.field.inputType === 'password' ? 'pr-10' : ''"
        v-model="model"
        v-bind="$attrs"
        :placeholder="props.field.placeholder"
        :disabled="props.field.readOnly"
        :type="props.passwordVisible ? 'text' : props.field.inputType"
        ref="input"
    />
</template>
