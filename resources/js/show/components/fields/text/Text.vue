<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { ShowTextFieldData } from "@/types";
    import { computed, provide, ref } from "vue";
    import FieldLayout from "../../FieldLayout.vue";
    import TextRenderer from "./TextRenderer.vue";
    import clip from "text-clipper";
    import { ShowFieldProps } from "@/show/types";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { useParentShow } from "@/show/useParentShow";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { Button } from "@/components/ui/button";

    const props = defineProps<ShowFieldProps<ShowTextFieldData>>();

    const expanded = ref(false);
    const show = useParentShow();

    const embedManager = new ContentEmbedManager(show, props.field.embeds, props.value?.embeds);
    const uploadManager = new ContentUploadManager(show, props.value?.uploads);

    provide('embedManager', embedManager);
    provide('uploadManager', uploadManager);

    const localizedValue = computed<string | null>(() => {
        return props.field.localized
            ? props.value.text?.[props.locale]
            : props.value?.text as string;
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
        <template v-if="currentContent && field.html">
            <TextRenderer
                class="content content-sm text-sm"
                :content="currentContent"
                :field="field"
            />
        </template>
        <template v-else>
            <div class="content content-sm text-sm">
                {{ currentContent }}
            </div>
        </template>

        <template v-if="collapsedContent">
            <Button class="mt-2 px-0" variant="link" @click.prevent="expanded = !expanded">
                <template v-if="expanded">
                    - {{ __('sharp::show.text.show_less') }}
                </template>
                <template v-else>
                    + {{ __('sharp::show.text.show_more') }}
                </template>
            </Button>
        </template>
    </FieldLayout>
</template>
