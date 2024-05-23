<script setup lang="ts">
    import { computed } from "vue";
    import { FormFieldProps } from "@/form/types";
    import { useParentForm } from "@/form/useParentForm";
    import { useId } from "radix-vue";
    import { __ } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import { cn } from "@/utils/cn";
    import { ToggleGroup, ToggleGroupItem } from "@/components/ui/toggle-group";

    const props = defineProps<FormFieldProps & {
        class?: string,
        fieldGroup?: boolean,
        shrinkEmptyLabel?: boolean,
    }>();
    const emit = defineEmits<{
        (e: 'label-click'),
        (e: 'locale-change', locale: string)
    }>();
    const form = useParentForm();
    const id = computed(() => useId(null, `form-field_${props.fieldErrorKey}`));
    const ariaDescribedBy = computed(() => [
        props.field.helpMessage && `${id.value}-help-message`,
        form.fieldHasError(props.field, props.fieldErrorKey) && `${id.value}-error`,
    ].filter(Boolean).join(' '));

    defineSlots<{
        default(props: { id: string, ariaDescribedBy: string }): any,
        'help-message'(): any
    }>();
</script>

<template>
    <div :class="cn('grid gap-2', props.class)"
        :role="fieldGroup ? 'group' : null"
        :aria-labelledby="fieldGroup ? `${id}-label` : null"
        :aria-describedby="fieldGroup ? ariaDescribedBy : null"
        :aria-invalid="form.fieldHasError(field, fieldErrorKey)"
        :style="field.extraStyle"
    >
        <div class="flex">
            <div class="flex mr-auto">
                <template v-if="field.label">
                    <Label
                        :id="`${id}-label`"
                        :as="fieldGroup ? 'div' : 'label'"
                        class="leading-5"
                        :class="{ 'text-destructive': form.fieldHasError(field, fieldErrorKey) }"
                        :for="id"
                        @click="$emit('label-click')"
                    >
                        {{ field.label }}
                    </Label>
                </template>
                <template v-else-if="row.length > 1">
                    <Label as="div" aria-hidden="true">&nbsp;</Label>
                </template>
            </div>
            <template v-if="'localized' in field && field.localized">
                <ToggleGroup class="h-3.5" :model-value="locale" @update:model-value="$emit('locale-change', $event)" type="single">
                    <template v-for="btnLocale in form.locales">
                        <ToggleGroupItem class="uppercase text-xs h-6" size="sm" :value="btnLocale">
                            {{ btnLocale }}
                            <template v-if="form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale)">
                                <svg class="ml-1 h-1.5 w-1.5 fill-destructive" viewBox="0 0 6 6" aria-hidden="true">
                                    <circle cx="3" cy="3" r="3" />
                                </svg>
                            </template>
                        </ToggleGroupItem>
                    </template>
                </ToggleGroup>
<!--                <nav class="flex items-center h-3.5">-->
<!--                    <template v-for="btnLocale in form.locales">-->
<!--                        <button-->
<!--                            class="flex items-center rounded-md px-2 py-1 text-xs font-medium uppercase"-->
<!--                            :class="[-->
<!--                                btnLocale === locale ? 'bg-indigo-100 text-indigo-700' :-->
<!--                                form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale) ? 'text-red-700' :-->
<!--                                'text-gray-500 hover:text-gray-700',-->
<!--                                form.fieldIsEmpty(field, value, btnLocale) ? 'italic' : ''-->
<!--                            ]"-->
<!--                            :aria-current="btnLocale === locale ? 'true' : null"-->
<!--                            @click="$emit('locale-change', btnLocale)"-->
<!--                        >-->
<!--                            {{ btnLocale }}-->

<!--                        </button>-->
<!--                    </template>-->
<!--                </nav>-->
            </template>
        </div>

        <slot v-bind="{ id, ariaDescribedBy }" />

        <template v-if="field.helpMessage || form.fieldHasError(field, fieldErrorKey)">
            <div class="grid gap-y-2">
                <template v-if="field.helpMessage || $slots['help-message']">
                    <p :id="`${id}-help-message`" class="text-sm text-muted-foreground">
                        <slot name="help-message">
                            {{ field.helpMessage }}
                        </slot>
                    </p>
                </template>

                <template v-if="form.fieldHasError(field, fieldErrorKey)">
                    <div :id="`${id}-error`" class="text-sm font-medium text-destructive">
                        <template v-if="form.fieldError(fieldErrorKey)">
                            {{ form.fieldError(fieldErrorKey) }}
                        </template>
                        <template v-else-if="'localized' in field && field.localized">
                            <template v-if="form.fieldError(`${fieldErrorKey}.${locale}`)">
                                {{ form.fieldError(`${fieldErrorKey}.${locale}`) }}
                            </template>
                            <template v-else>
                                {{ __('sharp::form.validation_error.localized', { locales: form.fieldLocalesContainingError(fieldErrorKey).map(l => l.toUpperCase()) }) }}
                            </template>
                        </template>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>
