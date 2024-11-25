<script setup lang="ts">
    import { computed, onUpdated, ref } from "vue";
    import { FormFieldProps } from "@/form/types";
    import { useParentForm } from "@/form/useParentForm";
    import { __, trans_choice } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import { cn } from "@/utils/cn";
    import { ToggleGroup, ToggleGroupItem } from "@/components/ui/toggle-group";
    import StickyTop from "@/components/StickyTop.vue";
    import { FormFieldData } from "@/types";
    import { useId } from "@/composables/useId";

    const props = defineProps<FormFieldProps<FormFieldData, any> & {
        class?: string,
        fieldGroup?: boolean,
        stickyLabel?: boolean,
    }>();
    const emit = defineEmits<{
        (e: 'label-click'),
        (e: 'locale-change', locale: string)
    }>();
    const form = useParentForm();
    const id = useId(`form-field_${props.fieldErrorKey}`);
    const ariaDescribedBy = computed(() => [
            props.field.helpMessage && `${id}-help-message`,
            form.fieldHasError(props.field, props.fieldErrorKey) && `${id}-error`,
        ].filter(Boolean).join(' ') || undefined
    );
    const slots = defineSlots<{
        default(props: { id: string, ariaDescribedBy: string }): any,
        'help-message'?(): any,
        'action'?(): any,
    }>();
    const hasLabelRow = computed(() =>
        props.field.label
        || props.row?.length > 1
        || 'localized' in props.field && props.field.localized
        || !!slots.action
    );
    const el = ref<HTMLElement>();
</script>

<template>
    <div :class="cn(
            'relative grid grid-cols-1 grid-rows-subgrid gap-2.5',
            hasLabelRow ? 'row-span-2' : '',
            props.class,
        )"
        :role="fieldGroup ? 'group' : null"
        :aria-labelledby="fieldGroup ? `${id}-label` : null"
        :aria-describedby="fieldGroup ? ariaDescribedBy : null"
        :aria-invalid="form.fieldHasError(field, fieldErrorKey)"
        ref="el"
    >
        <template v-if="hasLabelRow">
            <component
                :is="stickyLabel ? StickyTop : 'div'"
                class="group"
                :class="{ 'top-[calc(var(--top-bar-height)+.625rem)] z-10 lg:sticky': stickyLabel }"
                v-slot="{ stuck = false } = {}"
            >
                <template v-if="stickyLabel">
                    <div class="absolute bg-background transition-colors hidden border-b -inset-x-6 -top-3 -bottom-2.5 lg:group-data-[stuck]:block"
                        :class="stuck ? 'border-border' : 'border-transparent'"></div>
                </template>
                <div class="relative flex" :class="{
                    'pr-4': !root, // in list items we add padding to prevent dropdown overlapping
                }">
                    <div class="flex mr-auto">
                        <template v-if="field.label">
                            <Label
                                :id="`${id}-label`"
                                :as="fieldGroup ? 'div' : 'label'"
                                class="leading-4 cursor-default"
                                :class="{ 'text-destructive dark:text-foreground': form.fieldHasError(field, fieldErrorKey) }"
                                :for="id"
                                @click="$emit('label-click')"
                            >
                                {{ field.label }}
                            </Label>
                        </template>
                    </div>
                    <template v-if="$slots.action">
                        <div class="grid items-center content-center h-3.5">
                            <slot name="action" />
                        </div>
                    </template>
                    <template v-if="'localized' in field && field.localized">
                        <ToggleGroup class="h-3.5" :model-value="locale" @update:model-value="$emit('locale-change', $event)" type="single">
                            <template v-for="btnLocale in form.locales">
                                <ToggleGroupItem class="uppercase text-xs h-6" size="sm" :value="btnLocale">
                                    {{ btnLocale }}
                                    <template v-if="form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale)">
                                        <svg class="ml-1 h-2 w-2 fill-destructive" viewBox="0 0 8 8" aria-hidden="true">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                    </template>
                                </ToggleGroupItem>
                            </template>
                        </ToggleGroup>
                    </template>
                </div>
            </component>
        </template>

        <!-- We wrap the field + error / description to have only 2 child elements max (for subgrid alignment) -->
        <div class="isolate">
            <slot v-bind="{ id, ariaDescribedBy }" />

            <template v-if="field.helpMessage || $slots['help-message'] || form.fieldHasError(field, fieldErrorKey)">
                <div class="mt-2 grid grid-cols-1 gap-y-2">
                    <template v-if="field.helpMessage || $slots['help-message']">
                        <div :id="`${id}-help-message`" class="grid grid-cols-1 gap-y-2">
                            <template v-if="field.helpMessage || $slots['help-message']">
                                <p class="text-xs text-muted-foreground leading-4">
                                    <template v-if="field.helpMessage">
                                        {{ field.helpMessage }}
                                    </template>
                                    <template v-else>
                                        <slot name="help-message"></slot>
                                    </template>
                                </p>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.fieldHasError(field, fieldErrorKey)">
                        <div :id="`${id}-error`" class="text-sm font-medium text-destructive leading-4">
                            <span class="dark:bg-destructive dark:text-destructive-foreground dark:py-1 dark:px-2 dark:rounded-md">
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
                            </span>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>
