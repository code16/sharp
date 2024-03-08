<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowTextFieldData } from "@/types";
    import { computed, provide, ref } from "vue";
    import FieldLayout from "../../FieldLayout.vue";
    import TextRenderer from "./TextRenderer.vue";
    import clip from "text-clipper";
    import { ShowFieldProps } from "../../../types";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { useParentShow } from "@/show/useParentShow";
    import { Show } from "@/show/Show";
    import { ContentUploadManager } from "@/content/ContentUploadManager";

    const props = defineProps<ShowFieldProps<ShowTextFieldData>>();

    const expanded = ref(false);
    const show = useParentShow();

    const embedManager = new ContentEmbedManager(show, props.field.embeds);
    const uploadManager = new ContentUploadManager(show);

    provide('embedManager', embedManager);
    provide('uploadManager', uploadManager);

    const formattedValue = ref(
        embedManager.withEmbedsUniqueId(
            uploadManager.withUploadsUniqueId(
                props.value
            )
        )
    );

    embedManager.resolveContentEmbeds(formattedValue.value);
    uploadManager.resolveContentUploads(formattedValue.value);

    const localizedValue = computed<string | null>(() => {
        return props.field.localized
            ? formattedValue.value?.[props.locale]
            : formattedValue.value as string;
    });

    function stripTags(html) {
        const el = document.createElement('div');
        el.innerHTML = html;
        return el.textContent;
    }

    function truncateToWords(text, count) {
        const matches = [...text.matchAll(/\S+\s*/g)];
        return matches.length > count
            ? matches.slice(0, count).map(match => match[0]).join('')
            : text;
    }

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
            ? clip(content, truncated.length + 2, field.html ? { html: true } : {})
            : null;
    });

    const currentContent = computed(() => {
        if(!localizedValue.value) {
            return null;
        }
        if(collapsedContent.value && !expanded.value) {
            return collapsedContent.value;
        }
        if(!props.field.html) {
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
                :field="field"
                :value="value"
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
