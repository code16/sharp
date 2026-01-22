<script setup lang="ts">
    import { computed, ref } from "vue";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { useParentForm } from "@/form/useParentForm";
    import { __, trans_choice } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import { cn } from "@/utils/cn";
    import StickyTop from "@/components/StickyTop.vue";
    import { FormFieldData } from "@/types";
    import { useId } from "@/composables/useId";

    import FormFieldLocaleSelect from "@/form/components/FormFieldLocaleSelect.vue";
    import FormFieldError from "@/form/components/FormFieldError.vue";

    defineOptions({
        inheritAttrs: false
    });

    const props = defineProps<FormFieldProps<FormFieldData, any> & {
        class?: string,
        fieldGroup?: boolean,
        stickyLabel?: boolean,
        ariaLabel?: string,
    }>();
    const emit = defineEmits<FormFieldEmits & {
        (e: 'label-click'),
        (e: 'locale-select:close-auto-focus', event: Event),
    }>();
    const form = useParentForm();
    const id = useId(`form-field_${props.fieldErrorKey}`);
    const ariaLabelledBy = computed(() => props.field.label ? `${id}-label` : null);
    const ariaDescribedBy = computed(() => [
            props.field.helpMessage && `${id}-help-message`,
            form.fieldHasError(props.field, props.fieldErrorKey) && `${id}-error`,
        ].filter(Boolean).join(' ') || undefined
    );
    const slots = defineSlots<{
        default(props: { id: string, ariaDescribedBy: string, ariaLabelledBy: string }): any,
        'help-message'?(): any,
        'action'?(): any,
    }>();
    const hasLabelRow = computed(() =>
        props.field.label
        || 'localized' in props.field && props.field.localized
        || !!slots.action
    );
    const el = ref<HTMLElement>();
</script>

<template>
    <div :class="cn(
            'relative grid grid-cols-1 grid-rows-subgrid gap-2.5',
            hasLabelRow
                ? 'row-span-2'
                : props.row?.length > 1 ? ['row-span-1 @md/field-container:row-span-2', /* '@3xl/root-card:@md/field-container:row-span-2' */] : '',
            props.class,
        )"
        :role="fieldGroup ? 'group' : null"
        :aria-label="ariaLabel"
        :aria-labelledby="fieldGroup ? ariaLabelledBy : null"
        :aria-describedby="fieldGroup ? ariaDescribedBy : null"
        :aria-invalid="form.fieldHasError(field, fieldErrorKey)"
        ref="el"
    >
        <template v-if="hasLabelRow || props.row?.length > 1">
            <component
                :is="stickyLabel ? StickyTop : 'div'"
                class="group"
                :class="[{
                    'top-[calc(var(--stacked-top)+.625rem)] in-[[role=dialog]]:top-2.5 z-5 lg:sticky': stickyLabel,
                    'hidden @md/field-container:block @3xl/root-card:@md/field-container:block': !hasLabelRow,
                }]"
                v-slot="{ stuck = false } = {}"
            >
                <template v-if="stickyLabel">
                    <div class="absolute bg-background transition-colors hidden border-b -inset-x-6 -top-3 -bottom-2.5 lg:group-data-stuck:block"
                        :class="stuck ? 'border-border' : 'border-transparent'"></div>
                </template>
                <div class="relative flex flex-row-reverse flex-wrap gap-4" :class="{
                    'pr-4': !root, // in list items we add padding to prevent dropdown overlapping
                }">
                    <div class="order-1 flex mr-auto">
                        <template v-if="field.label">
                            <Label
                                :id="`${id}-label`"
                                :as="fieldGroup ? 'div' : 'label'"
                                class="leading-4 cursor-default"
                                :class="{
                                    'text-destructive': form.fieldHasError(field, fieldErrorKey),
                                }"
                                :for="id"
                                @click="$emit('label-click')"
                            >
                                {{ field.label }}
                            </Label>
                        </template>
                    </div>
                    <template v-if="$slots.action">
                        <div class="ml-auto grid items-center content-center h-3.5">
                            <slot name="action" />
                        </div>
                    </template>
                    <template v-if="'localized' in field && field.localized">
                        <FormFieldLocaleSelect
                            v-bind="props"
                            is-field-layout
                            @locale-change="emit('locale-change', $event)"
                            @close-auto-focus="emit('locale-select:close-auto-focus', $event)"
                        />
                    </template>
<!--                    <template v-if="'localized' in field && field.localized">-->
<!--                        <div class="ml-auto flex items-center h-3.5 gap-0 md:gap-1">-->
<!--                            <template v-for="btnLocale in form.locales">-->
<!--                                <Toggle class="uppercase text-xs h-6"-->
<!--                                    :class="form.fieldIsEmpty(props.field, props.value, btnLocale) ? 'text-foreground/50' : ''"-->
<!--                                    :model-value="locale === btnLocale"-->
<!--                                    size="sm"-->
<!--                                    :value="btnLocale"-->
<!--                                    @update:model-value="$emit('locale-change', btnLocale)"-->
<!--                                >-->
<!--                                    {{ btnLocale }}-->
<!--                                    <template v-if="form.fieldLocalesContainingError(fieldErrorKey).includes(btnLocale)">-->
<!--                                        <svg class="ml-1 h-2 w-2 fill-destructive" viewBox="0 0 8 8" aria-hidden="true">-->
<!--                                            <circle cx="4" cy="4" r="3" />-->
<!--                                        </svg>-->
<!--                                    </template>-->
<!--                                </Toggle>-->
<!--                            </template>-->
<!--                        </div>-->
<!--                    </template>-->
                </div>
            </component>
        </template>

        <!-- We wrap the field + error / description to have only 2 child elements max (for subgrid alignment) -->
        <div class="isolate">
            <slot v-bind="{ id, ariaDescribedBy, ariaLabelledBy }" />

            <template v-if="field.helpMessage || $slots['help-message'] || form.fieldHasError(field, fieldErrorKey)">
                <div class="mt-2 grid grid-cols-1 gap-y-2">
                    <template v-if="field.helpMessage || $slots['help-message']">
                        <div :id="`${id}-help-message`" class="grid grid-cols-1 gap-y-2">
                            <template v-if="field.helpMessage || $slots['help-message']">
                                <div class="text-xs text-muted-foreground leading-4">
                                    <template v-if="field.helpMessage">
                                        <div class="content" v-html="field.helpMessage"></div>
                                    </template>
                                    <template v-else>
                                        <slot name="help-message"></slot>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template v-if="form.fieldHasError(field, fieldErrorKey)">
                        <div :id="`${id}-error`" class="mb-1 text-sm text-destructive leading-4">
                            <FormFieldError v-bind="props" />
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>
