<template>
    <div>
        <template v-if="markdown">
            <MarkdownContentEditor
                v-bind="{...$props, ...$attrs}"
                v-on="$listeners"
                :extensions="extensions"
            />
        </template>
        <template v-else>
            <HtmlContentEditor
                v-bind="{...$props, ...$attrs}"
                v-on="$listeners"
                :extensions="extensions"
            />
        </template>
    </div>
</template>

<script>
    import HtmlContentEditor from './modes/html/Wysiwyg.vue';
    import MarkdownContentEditor from './modes/markdown/Markdown.vue';
    import { getDefaultExtensions, getUploadExtension } from "./extensions";
    import { getEmbedExtension } from "./extensions/embed";
    import { editorProps } from "./index";

    export default {
        components: {
            HtmlContentEditor,
            MarkdownContentEditor,
        },
        inject: ['$form'],
        props: {
            ...editorProps,
        },
        computed: {
            extensions() {
                const extensions = [
                    ...getDefaultExtensions({
                        placeholder: this.placeholder,
                        toolbar: this.toolbar,
                        inline: this.inline,
                    }),
                    ...this.embedExtensions,
                    this.uploadExtension,
                ];

                return extensions.filter(extension => !!extension);
            },
            uploadExtension() {
                if(!this.embeds?.upload) {
                    return null;
                }
                return getUploadExtension.call(this, {
                    fieldProps: this.embeds.upload,
                    uniqueIdentifier: this.uniqueIdentifier,
                    fieldConfigIdentifier: this.fieldConfigIdentifier,
                    form: this.$form,
                });
            },
            embedExtensions() {
                const { upload, ...embeds } = this.embeds ?? {};
                return Object.entries(embeds)
                    .map(([embedKey, embedOptions]) =>
                        getEmbedExtension({
                            embedKey,
                            embedOptions,
                            form: this.$form,
                        })
                    );
            },
        },
    }
</script>
