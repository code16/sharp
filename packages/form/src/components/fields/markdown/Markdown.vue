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
    import HorizontalRule from '@tiptap/extension-horizontal-rule';
    import MenuBar from "./toolbar/MenuBar";
    import localize from '../../../mixins/localize/editor';
    import { Upload } from "./extensions/upload/upload";
    import { TrailingNode } from "./extensions/trailing-node";
    import UploadFileInput from "./extensions/upload/UploadFileInput";
    import BubbleMenu from "./BubbleMenu";
    import { filesEquals } from "../../../util/upload";

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
                    HorizontalRule,
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
                    fieldProps: {
                        ...this.innerComponents.upload,
                        uniqueIdentifier: this.uniqueIdentifier,
                        fieldConfigIdentifier: this.fieldConfigIdentifier,
                    },
                    findFile: attrs => {
                        return this.value.files?.find(file => filesEquals(attrs, file));
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
                            files: this.value.files?.filter(file => !filesEquals(file, value)),
                        });
                    },
                    onUpdate: (value) => {
                        this.$emit('input', {
                            ...this.value,
                            files: this.value.files?.map(file => filesEquals(file, value) ? value : file),
                        });
                    },
                });
            },
            createEditor() {
                const MarkdownEditor = createMarkdownEditor(Editor);
                const markdownExtensions = [];
                const extensions = [
                    StarterKit.configure({
                        horizontalRule: false,
                    }),
                    Table,
                    TableRow,
                    TableHeader,
                    TableCell,
                    Image,
                    HorizontalRule.extend({
                        selectable: false,
                    }),
                    Link.configure({
                        openOnClick: false,
                    }),
                    TrailingNode,
                ]

                if(this.hasUpload) {
                    extensions.push(this.getUploadExtension());
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
