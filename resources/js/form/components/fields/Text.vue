<script setup lang="ts">
    import { FormTextFieldData } from "@/types";
    import { computed, ref } from "vue";
    import { normalizeText } from "../../util/text";
    import { validateTextField } from "../../util/validation";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { Input } from "@/components/ui/input";
    import { Button } from "@/components/ui/button";
    import { Eye, EyeOff } from 'lucide-vue-next';
    import { ComboboxAnchor, ComboboxPortal, ComboboxRoot, ComboboxInput } from "reka-ui";
    import { CommandGroup, CommandItem, CommandList } from "@/components/ui/command";
    import { useFullTextSearch } from "@/composables/useFullTextSearch";

    const props = defineProps<FormFieldProps<FormTextFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormTextFieldData>>();

    const input = ref();
    const passwordVisible = ref(false);
    const suggestionsOpen = ref(false);

    const currentText = computed(() =>
        props.field.localized && typeof props.value === 'object' ? props.value?.[props.locale] : (props.value as string)
    );

    const { fullTextSearch } = useFullTextSearch(
        () => props.field.suggestions?.map(suggestion => ({ suggestion })) ?? null,
        { id: 'suggestion', searchKeys: ['suggestion'] }
    );
    const filteredSuggestions = computed(() => {
        const query = currentText.value;
        return (query ? fullTextSearch(query) : props.field.suggestions?.map(suggestion => ({ suggestion })) ?? [])
            .map(result => result.suggestion);
    });

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
    }

    function onSuggestionSelect(suggestion: string) {
        onInput(suggestion);
        suggestionsOpen.value = false;
    }

    defineExpose({
        focus: () => input.value.$el.focus(),
    });
</script>

<template>
    <FormFieldLayout v-bind="props" @locale-change="emit('locale-change', $event)" v-slot="{ id, ariaDescribedBy }">
        <ComboboxRoot
            v-if="field.suggestions?.length"
            v-model:open="suggestionsOpen"
            ignore-filter
            open-on-focus
            open-on-click
        >
            <ComboboxAnchor class="relative">
                <ComboboxInput
                    :model-value="currentText"
                    :display-value="() => currentText ?? ''"
                    @update:model-value="onInput"
                    as-child
                >
                    <Input
                        :id="id"
                        :class="field.inputType === 'password' ? 'pr-10' : ''"
                        :placeholder="field.placeholder"
                        :disabled="field.readOnly"
                        :aria-describedby="ariaDescribedBy"
                        :type="passwordVisible ? 'text' : field.inputType"
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
            </ComboboxAnchor>
            <ComboboxPortal>
                <CommandList
                    v-if="filteredSuggestions.length"
                    position="popper"
                    position-strategy="absolute"
                    :avoid-collisions="false"
                    class="z-50 w-(--reka-popper-anchor-width) rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                >
                    <CommandGroup>
                        <template v-for="suggestion in filteredSuggestions" :key="suggestion">
                            <CommandItem :value="suggestion" @select.prevent="onSuggestionSelect(suggestion)">
                                {{ suggestion }}
                            </CommandItem>
                        </template>
                    </CommandGroup>
                </CommandList>
            </ComboboxPortal>
        </ComboboxRoot>
        <div v-else class="relative">
            <Input
                :id="id"
                :class="field.inputType === 'password' ? 'pr-10' : ''"
                :model-value="currentText"
                :placeholder="field.placeholder"
                :disabled="field.readOnly"
                :aria-describedby="ariaDescribedBy"
                :type="passwordVisible ? 'text' : field.inputType"
                @update:model-value="onInput"
                ref="input"
            />
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
    </FormFieldLayout>
</template>
