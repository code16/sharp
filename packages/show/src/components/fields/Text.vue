<template>
    <FieldLayout class="ShowTextField" :label="label">
        <div class="ShowTextField__content">
            <template v-if="html">
                <div v-html="currentContent"></div>
            </template>
            <template v-else>
                {{ currentContent }}
            </template>
        </div>

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
    import { syncVisibility } from "../../util/fields/visiblity";
    import { truncateToWords, stripTags } from "../../util/fields/text";
    import FieldLayout from "../FieldLayout";

    export default {
        mixins: [Localization],
        components: {
            FieldLayout,
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
            currentContent() {
                if(this.hasCollapsed && !this.expanded) {
                    return this.collapsedContent;
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
                const html = this.value.trim();
                const text = stripTags(html);
                const truncated = truncateToWords(text, this.collapseToWordCount);
                return truncated.length < text.length
                    ? clip(html, truncated.length + 2, { html: this.html })
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
