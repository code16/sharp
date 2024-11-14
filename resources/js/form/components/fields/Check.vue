<script setup lang="ts">
    import { FormCheckFieldData } from "@/types";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Label } from "@/components/ui/label";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormCheckFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormCheckFieldData>>();
</script>

<template>
    <FormFieldLayout v-bind="props">
        <div class="group/control flex items-center gap-x-3 h-10">
            <Checkbox
                :id="fieldErrorKey"
                :model-value="!!value"
                :disabled="field.readOnly"
                @update:model-value="emit('input', $event)"
            />
            <Label class="py-1" :for="fieldErrorKey">
                {{ field.text }}
            </Label>
        </div>
    </FormFieldLayout>
</template>
