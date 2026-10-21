<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { computed, ref } from "vue";
    import { normalizeText } from "@/form/util/text";
    import { validateTextField } from "@/form/util/validation";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import TextInput from "@/form/components/fields/text/TextInput.vue";
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

    const input = ref<InstanceType<typeof TextInput>>();
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
        focus: () => input.value.focus(),
    });
</script>

<template>
    <FormFieldLayout v-bind="props" @locale-change="emit('locale-change', $event)" v-slot="{ id, ariaDescribedBy }">
        <div class="relative">
            <template v-if="props.field.suggestionType">
                <Combobox
                    ignore-filter
                    :reset-search-term-on-blur="false"
                    :reset-search-term-on-select="false"
                    :open-on-focus="!textValue"
                    :open-on-click="!textValue"
                    @update:open="$event ? onOpen() : null"
                >
                    <ComboboxAnchor class="w-full">
                        <ComboboxInput :model-value="textValue" :display-value="() => textValue" as-child>
                            <TextInput
                                v-bind="props"
                                :id="id"
                                :aria-describedby="ariaDescribedBy"
                                :model-value="textValue"
                                @update:model-value="onInput"
                                ref="input"
                            />
                        </ComboboxInput>
                    </ComboboxAnchor>
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
                </Combobox>
            </template>
            <template v-else>
                <TextInput
                    v-bind="props"
                    :id="id"
                    :aria-describedby="ariaDescribedBy"
                    :model-value="textValue"
                    @update:model-value="onInput"
                    ref="input"
                />
            </template>
        </div>
    </FormFieldLayout>
</template>
