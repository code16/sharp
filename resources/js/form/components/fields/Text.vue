<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { computed, ref } from "vue";
    import { normalizeText } from "@/form/util/text";
    import { validateTextField } from "@/form/util/validation";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { Eye, EyeOff } from 'lucide-vue-next';
    import { useFullTextSearch } from "@/composables/useFullTextSearch";
    import { ComboboxInput } from "reka-ui";
    import {
        Combobox,
        ComboboxAnchor,
        ComboboxGroup,
        ComboboxItem,
        ComboboxList, ComboboxViewport
    } from "@/components/ui/combobox";

    const props = defineProps<FormFieldProps<FormTextFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTextFieldData>>();

    const input = ref();
    const passwordVisible = ref(false);
    const textValue = computed(() =>
        props.field.localized && typeof props.value === 'object'
            ? props.value?.[props.locale]
            : (props.value as string)
    )

    const { fullTextSearch } = useFullTextSearch(
        () => props.field.suggestions?.map(suggestion => ({ suggestion })),
        { id: 'suggestion', searchKeys: ['suggestion'] }
    );
    const filteredSuggestions = ref([]);

    function filterSuggestions(query: string) {
        if(props.field.suggestionType === 'local') {
            filteredSuggestions.value = query
                ? fullTextSearch(query).map(result => result.suggestion)
                : props.field.suggestions ?? [];
        }
    }

    function onInput(inputValue: string) {
        const value = normalizeText(inputValue);
        const error = validateTextField(value, {
            maxlength: props.field.maxLength,
        });

        if(props.field.localized) {
            emit('input',
                typeof props.value === 'object'
                    ? { ...props.value, [props.locale]: value }
                    : { [props.locale]: value },
                { error }
            );
        } else {
            emit('input', value, { error });
        }

        filterSuggestions(inputValue);
    }

    function onSuggestionSelect(suggestion: string) {
        onInput(suggestion);
    }

    function onOpen() {
        filterSuggestions(textValue.value);
    }

    defineExpose({
        focus: () => input.value.$el.focus(),
    });
</script>

<template>
    <FormFieldLayout v-bind="props" @locale-change="emit('locale-change', $event)" v-slot="{ id, ariaDescribedBy }">
        <Combobox
            ignore-filter
            :open-on-focus="props.field.suggestionType && !textValue"
            :open-on-click="props.field.suggestionType && !textValue"
            @update:open="$event ? onOpen() : null"
        >
            <ComboboxAnchor class="w-full">
                <div class="relative">
                    <ComboboxInput :model-value="textValue" as-child>
                        <Input
                            :id="id"
                            :class="field.inputType === 'password' ? 'pr-10' : ''"
                            :model-value="textValue"
                            :placeholder="field.placeholder"
                            :disabled="field.readOnly"
                            :aria-describedby="ariaDescribedBy"
                            :type="passwordVisible ? 'text' : field.inputType"
                            @update:model-value="onInput"
                            ref="input"
                        />
                    </ComboboxInput>
                    <template v-if="field.inputType === 'password'">
                        <Button class="absolute size-9.5 right-px top-px rounded-[calc(var(--radius)-3px)]" size="icon" variant="ghost" @click="passwordVisible = !passwordVisible">
                            <template v-if="passwordVisible">
                                <EyeOff />
                            </template>
                            <template v-else>
                                <Eye />
                            </template>
                        </Button>
                    </template>
                </div>
            </ComboboxAnchor>

            <template v-if="props.field.suggestionType">
                <ComboboxList class="w-(--reka-popper-anchor-width)" hide-when-empty>
                    <ComboboxViewport>
                        <ComboboxGroup>
                            <template v-for="suggestion in filteredSuggestions" :key="suggestion">
                                <ComboboxItem :value="suggestion" @select="onSuggestionSelect(suggestion)">
                                    {{ suggestion }}
                                </ComboboxItem>
                            </template>
                        </ComboboxGroup>
                    </ComboboxViewport>
                </ComboboxList>
            </template>
        </Combobox>
    </FormFieldLayout>
</template>
