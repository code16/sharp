<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { ref } from "vue";
    import { FormFieldProps } from "@/form/types";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { Eye, EyeOff } from 'lucide-vue-next';

    defineOptions({
        inheritAttrs: false,
    });

    const props = defineProps<FormFieldProps<FormTextFieldData>>();
    const model = defineModel<string>();

    const input = ref();
    const passwordVisible = ref(false);

    defineExpose({
        focus: () => input.value.$el.focus(),
    });
</script>

<template>
    <!-- multi-root: when used in <ComboboxInput as-child>, reka-ui resolves the <input> as the first element -->
    <Input
        v-bind="$attrs"
        :class="props.field.inputType === 'password' ? 'pr-10' : ''"
        v-model="model"
        :placeholder="props.field.placeholder"
        :disabled="props.field.readOnly"
        :type="passwordVisible ? 'text' : props.field.inputType"
        ref="input"
    />
    <template v-if="props.field.inputType === 'password'">
        <Button class="absolute size-9.5 right-px top-px rounded-[calc(var(--radius)-3px)]" size="icon" variant="ghost" @click="passwordVisible = !passwordVisible">
            <template v-if="passwordVisible">
                <EyeOff />
            </template>
            <template v-else>
                <Eye />
            </template>
        </Button>
    </template>
</template>
