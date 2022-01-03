<template>
    <FieldLayout class="ShowTextField" :class="classes" :label="label">
        <template v-if="html">
            <TextRenderer
                class="ShowTextField__content"
                :content="currentContent"
            />
        </template>
        <template v-else>
            <div class="ShowTextField__content">{{ currentContent }}</div>
        </template>
        <template v-if="hasCollapsed">
            <div class="mt-2">
                <a href="#" class="ShowTextField__more" @click.prevent="handleToggleClicked">
                    <template v-if="expanded">
                        - {{ l('show.text.show_less') }}
                    </template>
                    <template v-else>
                        + {{ l('show.text.show_more') }}
                    </template>
                </a>
            </div>
        </template>
    </FieldLayout>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import clip from 'text-clipper';
    import { syncVisibility } from "../../../util/fields/visiblity";
    import { truncateToWords, stripTags } from "../../../util/fields/text";
    import FieldLayout from "../../FieldLayout";
    import TextRenderer from "./TextRenderer";

    export default {
        mixins: [Localization],
        components: {
            FieldLayout,
            TextRenderer,
        },
        props: {
            value: String,
            collapseToWordCount: Number,
            label: String,
            emptyVisible: Boolean,
            html: Boolean,
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
            currentContent() {
                if(!this.value) {
                    return null;
                }
                if(this.hasCollapsed && !this.expanded) {
                    return this.collapsedContent;
                }
                if(!this.html) {
                    return stripTags(this.value).trim();
                }
                return this.value;
            },
            hasCollapsed() {
                return !!this.collapsedContent;
            },
            collapsedContent() {
                if(!this.collapseToWordCount || !this.value) {
                    return null;
                }
                const value = this.value.trim();
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
