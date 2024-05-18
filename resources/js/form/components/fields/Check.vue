<script setup lang="ts">
    import { FormCheckFieldData } from "@/types";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { Checkbox } from "@/components/ui/checkbox";
    import { Label } from "@/components/ui/label";

    const props = defineProps<FormFieldProps<FormCheckFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormCheckFieldData>>();
</script>

<template>
    <div class="flex items-center space-x-2">
        <Checkbox
            :id="fieldErrorKey"
            :checked="!!value"
            :disabled="field.readOnly"
            @update:checked="emit('input', $event)"
        />
        <Label :for="fieldErrorKey">
            {{ field.text }}
        </Label>
    </div>
</template>
