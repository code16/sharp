<template>
    <div class="SharpMarkdown editor" :style="style">
        <div class="card">
            <div class="card-header">
                <template v-if="editor">
                    <MenuBar :editor="editor" :toolbar="toolbar" />
                </template>
            </div>
            <EditorContent :editor="editor" />
        </div>
    </div>
</template>

<script>
    import { createMarkdownEditor } from 'tiptap-markdown';
    import { Editor, EditorContent } from '@tiptap/vue-2';
    import StarterKit from '@tiptap/starter-kit';
    import Table from '@tiptap/extension-table';
    import TableRow from '@tiptap/extension-table-row';
    import TableCell from '@tiptap/extension-table-cell';
    import TableHeader from '@tiptap/extension-table-header';
    import Image from '@tiptap/extension-image';
    import Link from '@tiptap/extension-link';
    import MenuBar from "./MenuBar";
    import localize from '../../../mixins/localize/editor';

    export default {
        mixins: [ localize({ textProp:'text' }) ],
        components: {
            MenuBar,
            EditorContent,
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
            }
        },
        computed: {
            style() {
                return {
                    '--height': `${this.height}px`,
                }
            }
        },
        methods: {
            handleUpdate() {
                const content = this.editor.getMarkdown();
                this.$emit('input', this.localizedValue(content));
            },
        },
        mounted() {
            const MarkdownEditor = createMarkdownEditor(Editor);
            this.editor = new MarkdownEditor({
                extensions: [
                    StarterKit,
                    Table,
                    TableRow,
                    TableHeader,
                    TableCell,
                    Image,
                    Link,
                ],
                content: this.localizedText,
                onUpdate: this.handleUpdate,
            });
            this.editor.view.dom.classList.add('card-body', 'form-control');
        },
    }
</script>
