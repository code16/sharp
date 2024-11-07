<script setup lang="ts">
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { FormSelectFieldData } from "@/types";
    import { __ } from "@/utils/i18n";
    import { ChevronDown } from "lucide-vue-next";
    import { Button } from "@/components/ui/button";
    import { ref } from "vue";
    import { Badge } from "@/components/ui/badge";
    import { isSelected } from "@/form/util/select";
    import {
        DropdownMenu, DropdownMenuCheckboxItem,
        DropdownMenuContent,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";

    const props = defineProps<FormFieldProps<FormSelectFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormSelectFieldData>>();
    const open = ref(false);

    function validate(value: typeof props.value) {
        if(props.field.multiple
            && props.field.maxSelected
            && (value as Array<string | number>)?.length > props.field.maxSelected
        ) {
            return __('sharp::form.select.validation.max_selected', { max_selected: props.field.maxSelected });
        }
        return null;
    }

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
                emit('input', option.id);
            }
        }
    }
</script>

<template>
    <FormFieldLayout v-bind="props" @label-click="open = true">
        <DropdownMenu v-model:open="open" :modal="false">
            <DropdownMenuTrigger as-child>
                <Button
                    class="w-full text-left justify-start font-normal h-auto min-h-10 py-2 gap-1 hover:bg-background"
                    variant="outline"
                    size="sm"
                    :disabled="field.readOnly"
                >
                    <template v-if="Array.isArray(value) ? value.length : value != null">
                        <template v-if="field.multiple">
                            <div class="flex flex-wrap gap-2">
                                <template v-for="option in field.options.filter((o) => (value as Array<string | number>)?.some(val => isSelected(o, val)))" :key="option.id">
                                    <Badge variant="secondary" class="block border-0 text-sm rounded-sm px-2 py-0.5 font-normal max-w-52 truncate transition-shadow">
                                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                                    </Badge>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <template v-for="option in [field.options.find((o) => isSelected(o, value))]">
                                {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option?.label }}
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
            <DropdownMenuContent class="w-[--radix-dropdown-menu-trigger-width] max-h-[min(24rem,var(--radix-dropdown-menu-content-available-height))] overflow-auto">
                <template v-for="option in field.options" :key="option.id">
                    <DropdownMenuCheckboxItem
                        :checked="Array.isArray(value) ? value.some(val => isSelected(option, val)) : isSelected(option, value)"
                        @update:checked="onChange($event, option)"
                        @select="field.multiple && $event.preventDefault()"
                    >
                        {{ field.localized && typeof option.label === 'object' ? option.label?.[locale] : option.label }}
                    </DropdownMenuCheckboxItem>
                </template>
            </DropdownMenuContent>
        </DropdownMenu>
    </FormFieldLayout>
</template>
