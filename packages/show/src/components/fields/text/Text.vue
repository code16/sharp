<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowTextFieldData } from "@/types";
    import { computed, ref } from "vue";
    import FieldLayout from "../../FieldLayout.vue";
    import TextRenderer from "./TextRenderer.vue";
    import { stripTags, truncateToWords } from "../../../util/fields/text";
    import clip from "text-clipper";

    const props = defineProps<{
        value: ShowTextFieldData['value'],
        field: Omit<ShowTextFieldData, 'value'>,
        locale: string,
        entityKey: string,
        instanceId: string,
    }>();

    const expanded = ref(false);

    const localizedValue = computed<string | null>(() => {
        const { field, locale, value } = props;
        return field.localized ? value?.[locale] : value;
    });

    const collapsedContent = computed(() => {
        const { field } = props;
        const value = localizedValue.value?.trim();
        if (!field.collapseToWordCount || !value) {
            return null;
        }
        const text = stripTags(value).trim();
        const content = field.html ? value.trim() : text;
        const truncated = truncateToWords(text, field.collapseToWordCount);
        return truncated.length < text.length
            ? clip(content, truncated.length + 2, { html: field.html })
            : null;
    });

    const currentContent = computed(() => {
        const { field } = props;
        if(!localizedValue.value) {
            return null;
        }
        if(collapsedContent.value && !expanded.value) {
            return collapsedContent.value;
        }
        if(!field.html) {
            return stripTags(localizedValue.value).trim();
        }
        return localizedValue.value;
    });
</script>

<template>
    <FieldLayout
        class="ShowTextField"
        :class="{
            'ShowTextField--html': field.html,
        }"
        :label="field.label"
    >
        <template v-if="field.html">
            <TextRenderer
                class="ShowTextField__content"
                :content="currentContent"
                :embeds="field.embeds"
                :entity-key="entityKey"
                :instance-id="instanceId"
            />
        </template>
        <template v-else>
            <div class="ShowTextField__content">{{ currentContent }}</div>
        </template>

        <template v-if="collapsedContent">
            <div class="mt-2">
                <a href="#" class="ShowTextField__more" @click.prevent="expanded = !expanded">
                    <template v-if="expanded">
                        - {{ __('sharp::show.text.show_less') }}
                    </template>
                    <template v-else>
                        + {{ __('sharp::show.text.show_more') }}
                    </template>
                </a>
            </div>
        </template>
    </FieldLayout>
</template>
