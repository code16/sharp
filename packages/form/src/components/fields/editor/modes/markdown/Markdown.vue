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
    import { defaultEditorOptions, getDefaultExtensions, getUploadExtension } from "../..";
    import { LocalizedEditor } from '../../../../../mixins/localize/editor';
    import LocalizedEditors from "../../LocalizedEditors";
    import ListDropdown from "../../toolbar/OptionsDropdown";

    export default {
        mixins: [
            LocalizedEditor
        ],
        components: {
            SharpEditor,
            LocalizedEditors,
            ListDropdown,
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
            handleUpdate(editor) {
                const content = editor.getMarkdown();
                this.$emit('input', this.localizedValue(content));
            },
            toolbarOptions(editor) {
                const options = [];
                const hasList = this.toolbar.some(button => button === 'bullet-list' || button === 'ordered-list');

                if(!this.tightListsOnly && hasList) {
                    options.push({
                        command: () => editor.chain().toggleTight().run(),
                        disabled: !editor.can().toggleTight(),
                        label: 'Toggle tight list',
                    });
                }

                return options;
            },

            createEditor({ content }) {
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
