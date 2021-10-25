<template>
    <SharpEditor
        class="SharpMarkdown"
        :editor="editor"
        v-bind="$props"
    >
    </SharpEditor>
</template>

<script>
    import { createMarkdownEditor, createMarkdownExtension } from 'tiptap-markdown';
    import { Editor } from '@tiptap/vue-2';
    import SharpEditor from '../editor/Editor';
    import { defaultEditorOptions, getDefaultExtensions, getUploadExtension } from "../editor";
    import localize from '../../../mixins/localize/editor';

    export default {
        mixins: [ localize({ textProp:'text' }) ],
        components: {
            SharpEditor,
        },
        inject: ['$form'],
        props: {
            id: String,
            value: {
                type: Object,
                default: ()=>({})
            },
            placeholder: String,
            toolbar: Array,
            minHeight: Number,
            maxHeight: Number,
            innerComponents: Object,
            nl2br: Boolean,
            tightListsOnly: Boolean,

            readOnly: Boolean,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
        },
        data() {
            return {
                editor: null,
            }
        },
        computed: {
            hasUpload() {
                return !!this.innerComponents?.upload;
            },
        },
        methods: {
            async handleUpdate() {
                await this.$nextTick();
                const content = this.editor.getMarkdown();
                this.$emit('input', this.localizedValue(content));
                console.log(this.editor.getHTML());
            },

            createEditor() {
                const MarkdownEditor = createMarkdownEditor(Editor);
                const extensions = [
                    ...getDefaultExtensions({
                        placeholder: this.placeholder,
                        toolbar: this.toolbar,
                    }),
                ]

                if(this.hasUpload) {
                    const Upload = getUploadExtension.call(this, {
                        fieldProps: this.innerComponents.upload,
                        uniqueIdentifier: this.uniqueIdentifier,
                        fieldConfigIdentifier: this.fieldConfigIdentifier,
                        form: this.$form,
                    });
                    extensions.push(Upload);
                }

                return new MarkdownEditor({
                    ...defaultEditorOptions,
                    extensions,
                    content: this.localizedText,
                    onUpdate: this.handleUpdate,
                    editable: !this.readOnly,
                    markdown: {
                        breaks: this.nl2br,
                    },
                });
            },
        },
        mounted() {
            this.editor = this.createEditor();
        },
        beforeDestroy() {
            this.editor.destroy();
        },
    }
</script>
