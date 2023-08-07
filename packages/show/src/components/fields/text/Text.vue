<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <FieldLayout class="ShowTextField" :class="classes" :label="label">
        <template v-if="html">
            <TextRenderer
                class="ShowTextField__content"
                :content="currentContent"
                :embeds="embeds"
            />
        </template>
        <template v-else>
            <div class="ShowTextField__content">{{ currentContent }}</div>
        </template>
        <template v-if="hasCollapsed">
            <div class="mt-2">
                <a href="#" class="ShowTextField__more" @click.prevent="handleToggleClicked">
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

<script lang="ts">
    import clip from 'text-clipper';
    import { syncVisibility } from "../../../util/fields/visiblity";
    import { truncateToWords, stripTags } from "../../../util/fields/text";
    import FieldLayout from "../../FieldLayout.vue";
    import TextRenderer from "./TextRenderer.vue";

    export default {
        components: {
            FieldLayout,
            TextRenderer,
        },
        props: {
            value: [Object, String],
            collapseToWordCount: Number,
            label: String,
            emptyVisible: Boolean,
            html: Boolean,
            localized: Boolean,
            locale: String,
            embeds: Object,
        },
        data() {
            return {
                expanded: false,
            }
        },
        computed: {
            classes() {
                return {
                    'ShowTextField--html': this.html,
                }
            },
            resolvedValue() {
                return this.localized
                    ? this.value?.[this.locale]
                    : this.value;
            },
            currentContent() {
                if(!this.resolvedValue) {
                    return null;
                }
                if(this.hasCollapsed && !this.expanded) {
                    return this.collapsedContent;
                }
                if(!this.html) {
                    return stripTags(this.resolvedValue).trim();
                }
                return this.resolvedValue;
            },
            hasCollapsed() {
                return !!this.collapsedContent;
            },
            collapsedContent() {
                if(!this.collapseToWordCount || !this.value) {
                    return null;
                }
                const value = this.resolvedValue.trim();
                const text = stripTags(value);
                const content = this.html ? value : text;
                const truncated = truncateToWords(text, this.collapseToWordCount);
                return truncated.length < text.length
                    ? clip(content, truncated.length + 2, { html: this.html })
                    : null;
            },
            isVisible() {
                return !!this.value || this.emptyVisible;
            },
        },
        methods: {
            handleToggleClicked() {
                this.expanded = !this.expanded;
            },
        },
        created() {
            syncVisibility(this, () => this.isVisible);
        },
    }
</script>
