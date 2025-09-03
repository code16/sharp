<script setup lang="ts">

import { __ } from "@/utils/i18n";
import { Select, SelectContent, SelectItem } from "@/components/ui/select";
import LocaleSelectTrigger from "@/components/LocaleSelectTrigger.vue";
import { FormFieldEmits, FormFieldProps } from "@/form/types";
import { FormFieldData } from "@/types";
import { useParentForm } from "@/form/useParentForm";

const props = defineProps<FormFieldProps<FormFieldData, any> & { teleportTo?: HTMLElement, isFieldLayout?: boolean }>();
const emit = defineEmits<FormFieldEmits>();
const form = useParentForm();
</script>

<template>
    <Select :model-value="props.locale" @update:model-value="emit('locale-change', $event as string)">
        <LocaleSelectTrigger
            class="ml-auto w-auto border-transparent hover:border-input aria-expanded:border-input"
            :class="{ '-my-2': isFieldLayout }"
            :aria-label="__('sharp::form.field_locale_selector.aria_label', { field_label:field.label })"
        />
        <SelectContent :teleport-to="teleportTo">
            <template v-for="itemLocale in form.locales" :key="itemLocale">
                <SelectItem :value="itemLocale">
                    <div class="flex items-center">
                        <span class="uppercase text-xs">{{ itemLocale }}</span>
                        <template v-if="form.fieldLocalesContainingError(fieldErrorKey).includes(itemLocale)">
                            <svg class="ml-1 h-2 w-2 fill-destructive" viewBox="0 0 8 8" aria-hidden="true">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                        </template>
                    </div>
                </SelectItem>
            </template>
        </SelectContent>
    </Select>
</template>
