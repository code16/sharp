<template>
    <div class="ShowTextField">
        <template v-if="label">
            <span class="ShowTextField__label">{{ label }}</span>
        </template>
        <div class="ShowTextField__content" v-html="currentContent" ref="content"></div>
        <template v-if="hasCollapsed">
            <div class="mt-2">
                <a href="#" class="text-decoration-none text-lowercase" @click.prevent="handleToggleClicked">
                    <template v-if="expanded">
                        - {{ l('show.text.show_less') }}
                    </template>
                    <template v-else>
                        + {{ l('show.text.show_more') }}
                    </template>
                </a>
            </div>
        </template>
    </div>
</template>

<script>
    import { Localization } from '../../../mixins';
    import clip from 'text-clipper';

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

    export default {
        mixins: [Localization],
        props: {
            value: String,
            collapseToWordCount: Number,
            label: String,
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
                    ? clip(html, truncated.length + 2, { html: true })
                    : null;
            },
        },
        methods: {
            handleToggleClicked() {
                this.expanded = !this.expanded;
            },
        },
    }
</script>