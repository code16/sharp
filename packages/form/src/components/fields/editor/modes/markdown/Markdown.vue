<template>
    <div>
        <LocalizedEditors
            :editor="editor"
            :name="uniqueIdentifier"
            :value="value"
            :locale="locale"
            :locales="locales"
            :create-editor="createEditor"
            v-slot="{ editor }"
        >
            <SharpEditor
                :editor="editor"
                :toolbar-options="toolbarOptions(editor)"
                v-bind="$props"
                @update="handleUpdate"
            />
        </LocalizedEditors>
    </div>
</template>

<script>
    import { createMarkdownEditor } from 'tiptap-markdown';
    import { Editor } from '@tiptap/vue-2';
    import SharpEditor from '../../Editor';
    import { defaultEditorOptions, editorProps } from "../..";
    import { LocalizedEditor } from '../../../../../mixins/localize/editor';
    import LocalizedEditors from "../../LocalizedEditors";
    import { lang } from "sharp";

    export default {
        mixins: [
            LocalizedEditor
        ],
        components: {
            SharpEditor,
            LocalizedEditors,
        },
        props: {
            ...editorProps,
            extensions: Array,
            toolbar: Array,
            nl2br: Boolean,
            tightListsOnly: Boolean,

            readOnly: Boolean,
            uniqueIdentifier: String,
        },
        data() {
            return {
                editor: null,
            }
        },
        methods: {
            handleUpdate(editor) {
                const content = editor.getMarkdown();
                this.$emit('input', this.localizedValue(content));
            },
            toolbarOptions(editor) {
                const options = [];
                const hasList = this.toolbar?.some(button => button === 'bullet-list' || button === 'ordered-list');

                if(!this.tightListsOnly && hasList) {
                    options.push({
                        command: () => editor.chain().toggleTight().run(),
                        disabled: !editor.can().toggleTight(),
                        label: lang('form.editor.dropdown.options.toggle_tight_list'),
                    });
                }

                return options;
            },

            createEditor({ content }) {
                const MarkdownEditor = createMarkdownEditor(Editor);

                return new MarkdownEditor({
                    ...defaultEditorOptions,
                    extensions: this.extensions,
                    content,
                    editable: !this.readOnly,
                    markdown: {
                        breaks: this.nl2br,
                    },
                });
            },
        },
        created() {
            if(!this.isLocalized) {
                this.editor = this.createEditor({
                    content: this.localizedText,
                });
            }
        },
        beforeDestroy() {
            this.editor?.destroy();
        },
    }
</script>
