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
                v-bind="$props"
                @update="handleUpdate"
            />
        </LocalizedEditors>
    </div>
</template>

<script>
    import { Editor } from '@tiptap/vue-2';
    import SharpEditor from '../../Editor';
    import { defaultEditorOptions, getDefaultExtensions, getUploadExtension } from "../..";
    import { LocalizedEditor } from "../../../../../mixins/localize/editor";
    import { normalizeHTML } from "./util";
    import LocalizedEditors from "../../LocalizedEditors";

    export default {
        mixins: [
            LocalizedEditor
        ],
        components: {
            SharpEditor,
            LocalizedEditors,
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
                const content = editor.getHTML();
                this.$emit('input', this.localizedValue(content));
            },

            createEditor({ content }) {
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

                return new Editor({
                    ...defaultEditorOptions,
                    extensions,
                    content: normalizeHTML(content),
                    editable: !this.readOnly,
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
