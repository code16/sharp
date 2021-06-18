<template>
    <div class="SharpMarkdown editor" :style="style">
        <div class="card">
            <div class="card-header">
                <template v-if="editor">
                    <MenuBar :id="uniqueIdentifier" :editor="editor" :toolbar="toolbar" />
                </template>
            </div>
            <EditorContent :editor="editor" />

            <template v-if="editor">
                <BubbleMenu
                    :id="uniqueIdentifier"
                    :editor="editor"
                    :toolbar="toolbar"
                    :ignored-extensions="bubbleMenuIgnoredExtensions"
                />
                <UploadFileInput :editor="editor"/>
            </template>
        </div>
    </div>
</template>

<script>
    import { createMarkdownEditor, createMarkdownExtension } from 'tiptap-markdown';
    import { Editor, EditorContent } from '@tiptap/vue-2';
    import StarterKit from '@tiptap/starter-kit';
    import Table from '@tiptap/extension-table';
    import TableRow from '@tiptap/extension-table-row';
    import TableCell from '@tiptap/extension-table-cell';
    import TableHeader from '@tiptap/extension-table-header';
    import Image from '@tiptap/extension-image';
    import Link from '@tiptap/extension-link';
    import Underline from '@tiptap/extension-underline';
    import MenuBar from "./toolbar/MenuBar";
    import localize from '../../../mixins/localize/editor';
    import { Upload } from "./extensions/upload/upload";
    import { TrailingNode } from "./extensions/trailing-node";
    import UploadFileInput from "./extensions/upload/UploadFileInput";
    import BubbleMenu from "./BubbleMenu";

    export default {
        mixins: [ localize({ textProp:'text' }) ],
        components: {
            EditorContent,
            BubbleMenu,
            MenuBar,
            UploadFileInput,
        },
        props: {
            id: String,
            value: {
                type: Object,
                default: ()=>({})
            },
            placeholder: String,
            toolbar: Array,
            height: {
                type: Number,
                default: 300
            },
            innerComponents: Object,

            readOnly: Boolean,
            uniqueIdentifier: String,
            fieldConfigIdentifier: String,
        },
        data() {
            return {
                editor: null,
                currentFileId: 0,
            }
        },
        computed: {
            style() {
                return {
                    '--height': `${this.height}px`,
                }
            },
            hasUpload() {
                return !!this.innerComponents?.upload;
            },
            bubbleMenuIgnoredExtensions() {
                return [
                    Upload,
                ]
            },
        },
        methods: {
            async handleUpdate() {
                await this.$nextTick();
                const content = this.editor.getMarkdown();
                this.$emit('input', this.localizedValue(content));
            },
            getUploadExtension() {
                return Upload.configure({
                    fieldProps: this.innerComponents.upload,
                    getFileByName: (name) => {
                        return this.value.files?.find(file => file.name === name);
                    },
                    onSuccess: (value) => {
                        this.$emit('input', {
                            ...this.value,
                            files: [...(this.value?.files ?? []), value],
                        });
                    },
                    onRemove: (value) => {
                        this.$emit('input', {
                            ...this.value,
                            files: this.value.files?.filter(file => file.name !== value.name),
                        });
                    },
                    onUpdate: (value) => {
                        this.$emit('input', {
                            ...this.value,
                            files: this.value.files?.map(file => file.name === value.name ? value : file),
                        });
                    },
                });
            },
            createEditor() {
                const MarkdownEditor = createMarkdownEditor(Editor);
                const markdownExtensions = [];
                const extensions = [
                    StarterKit,
                    Table,
                    TableRow,
                    TableHeader,
                    TableCell,
                    Image,
                    Link.configure({
                        openOnClick: false,
                    }),
                    Underline,
                    TrailingNode,
                ]

                if(this.hasUpload) {
                    extensions.push(this.getUploadExtension());

                    // todo remove this when back handle <x-sharp-media>
                    markdownExtensions.push(createMarkdownExtension(Upload, {
                        serialize(state, node) {
                            if(node.attrs.value?.name) {
                                state.write("![](" + state.esc(node.attrs.value.name) + ")");
                            }
                        },
                    }));
                }

                this.editor = new MarkdownEditor({
                    extensions,
                    content: this.localizedText,
                    onUpdate: this.handleUpdate,
                    markdown: {
                        extensions: markdownExtensions,
                    }
                });

                this.editor.view.dom.classList.add(
                    'card-body',
                    'form-control',
                    'editor__content',
                    'SharpMarkdown__form-control',
                );
            },
        },
        mounted() {
            this.createEditor();
        },
        beforeDestroy() {
            this.editor.destroy()
        },
    }
</script>
