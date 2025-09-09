<script setup lang="ts">

import { trans_choice } from "@/utils/i18n";
import { FormFieldProps } from "@/form/types";
import { FormFieldData } from "@/types";
import { useParentForm } from "@/form/useParentForm";

const form = useParentForm();
defineProps<FormFieldProps<FormFieldData, any>>();
defineOptions({ inheritAttrs: false });
</script>

<template>
    <template v-if="form.fieldError(fieldErrorKey)">
        {{ form.fieldError(fieldErrorKey) }}
    </template>
    <template v-else-if="'localized' in field && field.localized">
        <template v-if="form.fieldError(`${fieldErrorKey}.${locale}`)">
            {{ form.fieldError(`${fieldErrorKey}.${locale}`) }}
        </template>
        <template v-else>
            {{
                trans_choice(
                    'sharp::form.validation_error.localized',
                    form.fieldLocalesContainingError(fieldErrorKey).length,
                    { locales: form.fieldLocalesContainingError(fieldErrorKey).map(l => l.toUpperCase()) }
                )
            }}
        </template>
    </template>
</template>
