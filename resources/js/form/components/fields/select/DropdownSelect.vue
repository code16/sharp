<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";
    import { __ } from "@/utils/i18n";
    import { ChevronDown, X, ListChecks } from "lucide-vue-next";
    import { Button } from "@/components/ui/button";
    import { computed, ref } from "vue";
    import { Badge } from "@/components/ui/badge";
    import { isSelected } from "@/form/util/select";
    import {
        DropdownMenu, DropdownMenuCheckboxItem,
        DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { useSelect } from "@/form/components/fields/select/useSelect";

    const props = defineProps<FormFieldProps<FormSelectFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();
    const open = ref(false);

    const { validate, isAllSelected, selectAll } = useSelect(props, emit);

    function onChange(checked: boolean, option: typeof props.field.options[0]) {
        if(props.field.multiple) {
            const value = props.field.options
                .filter(o => o.id === option.id ? checked : (props.value as Array<string | number>)?.some(val => isSelected(o, val)))
                .map(o => o.id);
            emit('input', value, { error: validate(value) });
        } else {
            if(isSelected(option, props.value) && props.field.clearable) {
                emit('input', null);
            } else {
                console.log(option.id);
                emit('input', option.id);
            }
        }
    }

    const showClear = computed(() =>
        (Array.isArray(props.value) ? props.value.length : props.value != null)
        && (props.field.clearable || props.field.showSelectAll && props.field.multiple)
    );
    const showSelectAll = computed(() => props.field.showSelectAll && props.field.multiple && !isAllSelected.value);
</script>

<template>
    <FormFieldLayout v-bind="props" @label-click="open = true" v-slot="{ ariaLabelledBy }">
        <DropdownMenu v-model:open="open" :modal="false">
            <DropdownMenuTrigger as-child>
                <Button
                    class="w-full text-left justify-start font-normal h-auto min-h-10 py-2 gap-1 hover:bg-background"
                    variant="outline"
                    size="sm"
                    role="combobox"
                    aria-autocomplete="none"
                    :aria-labelledby="ariaLabelledBy"
                    :disabled="field.readOnly"
                >
                    <template v-if="Array.isArray(value) ? value.length : value != null">
                        <template v-if="field.multiple">
                            <span class="flex flex-wrap gap-2">
                                <template v-for="option in field.options.filter((o) => (value as Array<string | number>)?.some(val => isSelected(o, val)))" :key="option.id">
                                    <Badge variant="secondary" class="block border-0 text-sm rounded-sm px-2 py-0.5 font-normal max-w-52 truncate transition-shadow">
                                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                                    </Badge>
                                </template>
                            </span>
                        </template>
                        <template v-else>
                            <template v-for="option in [field.options.find((o) => isSelected(o, value))]">
                                <span class="truncate min-w-0 flex-1">
                                    {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option?.label }}
                                </span>
                            </template>
                        </template>
                    </template>
                    <template v-else>
                        <span class="text-muted-foreground">
                            {{ __('sharp::form.multiselect.placeholder') }}
                        </span>
                    </template>

                    <ChevronDown class="w-4 h-4 opacity-50 shrink-0 ml-auto" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent class="w-[--reka-dropdown-menu-trigger-width] max-h-[min(24rem,var(--reka-dropdown-menu-content-available-height))] overflow-auto">
                <template v-for="option in field.options" :key="option.id">
                    <DropdownMenuCheckboxItem
                        :model-value="Array.isArray(value) ? value.some(val => isSelected(option, val)) : isSelected(option, value)"
                        @update:model-value="onChange($event, option)"
                        @select="field.multiple && $event.preventDefault() /* prevent closing if multiple */"
                    >
                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                    </DropdownMenuCheckboxItem>
                </template>
                <template v-if="showClear || showSelectAll">
                    <div class="isolate sticky bottom-0 border-b border-transparent">
                        <div class="bg-popover absolute -inset-1 -bottom-1 top-0 -z-10"></div>
                        <DropdownMenuSeparator />
                        <div class="flex gap-2">
                            <template v-if="showClear">
                                <DropdownMenuItem class="flex-1 font-medium text-center justify-center" @select="emit('input', null); field.multiple && $event.preventDefault()">
                                    {{ __('sharp::form.select.clear') }}
                                </DropdownMenuItem>
                            </template>
                            <template v-if="showSelectAll">
                                <DropdownMenuItem class="flex-1 font-medium text-center justify-center" @select.prevent="selectAll()">
                                    {{ __('sharp::form.select.select_all') }}
                                </DropdownMenuItem>
                            </template>
                        </div>
                    </div>
                </template>
            </DropdownMenuContent>
        </DropdownMenu>
    </FormFieldLayout>
</template>
