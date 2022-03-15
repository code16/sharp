<template>
    <div>
        <template v-if="markdown">
            <MarkdownContentEditor
                v-bind="$props"
                v-on="$listeners"
                :extensions="extensions"
                :embeds="embeds"
            />
        </template>
        <template v-else>
            <HtmlContentEditor
                v-bind="$props"
                v-on="$listeners"
                :extensions="extensions"
                :embeds="embeds"
            />
        </template>
    </div>
</template>

<script>
    import HtmlContentEditor from './modes/html/Wysiwyg';
    import MarkdownContentEditor from './modes/markdown/Markdown';
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
                    }),
                    ...this.embedExtensions,
                    this.uploadExtension,
                ];

                return extensions.filter(extension => !!extension);
            },
            uploadExtension() {
                if(!this.innerComponents?.upload) {
                    return null;
                }
                return getUploadExtension.call(this, {
                    fieldProps: this.innerComponents.upload,
                    uniqueIdentifier: this.uniqueIdentifier,
                    fieldConfigIdentifier: this.fieldConfigIdentifier,
                    form: this.$form,
                });
            },
            embedExtensions() {
                const { upload, ...embeds } = this.innerComponents ?? {};
                return Object.entries(embeds)
                    .map(([embedKey, embedProps]) =>
                        getEmbedExtension({
                            embedKey,
                            props: embedProps,
                            form: this.$form,
                        })
                    );
            },
            embeds() {
                const { upload, ...embeds } = this.innerComponents ?? {};
                return Object.values(embeds);
            },
        },
    }
</script>
