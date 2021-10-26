<template>
    <div>
        <template v-if="markdown">
            <MarkdownContentEditor
                v-bind="$props"
                v-on="$listeners"
            />
        </template>
        <template v-else>
            <HtmlContentEditor
                v-bind="$props"
                v-on="$listeners"
            />
        </template>
    </div>
</template>

<script>
    import HtmlContentEditor from './modes/html/Wysiwyg';
    import MarkdownContentEditor from './modes/markdown/Markdown';

    export default {
        components: {
            HtmlContentEditor,
            MarkdownContentEditor,
        },
        props: {
            markdown: Boolean,
            locale: String,
            localized: Boolean,
            ...HtmlContentEditor.props,
            ...MarkdownContentEditor.props
        },
    }
</script>
