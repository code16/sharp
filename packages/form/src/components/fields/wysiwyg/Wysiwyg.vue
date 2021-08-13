<template>
    <SharpEditor
        class="SharpWysiwyg"
        :editor="editor"
        v-bind="$props"
    >
    </SharpEditor>
</template>

<script>
    import { Editor } from '@tiptap/vue-2';
    import SharpEditor from '../editor/Editor';
    import { getDefaultExtensions, getUploadExtension } from "../editor";
    import localize from '../../../mixins/localize/editor';
    import { normalizeHTML } from "./util";

    export default {
        mixins: [ localize({ textProp:'text' }) ],
        components: {
            SharpEditor,
        },
        props: {
            id: String,
            value: {
                type: Object,
                default: ()=>({})
            },
            placeholder: String,
            toolbar: Array,
            height: Number,
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
            async handleUpdate() {
                await this.$nextTick();
                const content = this.editor.getHTML();
                this.$emit('input', this.localizedValue(content));
            },

            createEditor() {
                const extensions = [
                    ...getDefaultExtensions({
                        placeholder: this.placeholder,
                        toolbar: this.toolbar,
                    }),
                ]

                if(this.hasUpload) {
                    const Upload = getUploadExtension.call(this, {
                        fieldProps: this.innerComponents.upload,
                    });
                    extensions.push(Upload);
                }

                return new Editor({
                    extensions,
                    content: normalizeHTML(this.localizedText),
                    onUpdate: this.handleUpdate,
                    editable: !this.readOnly,
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
