<template>
    <div class="editor" :class="classes" :style="style">
        <div class="card">
            <div class="card-header">
                <template v-if="editor">
                    <MenuBar :id="uniqueIdentifier" :editor="editor" :toolbar="toolbar" />
                </template>
            </div>

            <EditorContent :editor="editor" />

            <template v-if="editor && !readOnly">
                <BubbleMenu
                    :id="uniqueIdentifier"
                    :editor="editor"
                    :toolbar="toolbar"
                    :ignored-extensions="bubbleMenuIgnoredExtensions"
                />
                <template v-if="hasUpload">
                    <UploadFileInput :editor="editor"/>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import { EditorContent } from '@tiptap/vue-2';
    import HorizontalRule from "@tiptap/extension-horizontal-rule";
    import { Upload } from "./extensions/upload/upload";
    import UploadFileInput from "./extensions/upload/UploadFileInput";
    import MenuBar from "./toolbar/MenuBar";
    import BubbleMenu from "./BubbleMenu";

    export default {
        components: {
            EditorContent,
            MenuBar,
            UploadFileInput,
            BubbleMenu,
        },
        props: {
            editor: Object,
            uniqueIdentifier: String,
            toolbar: Array,
            height: {
                type: Number,
                default: 300
            },
            readOnly: Boolean,
        },
        computed: {
            classes() {
                return {
                    'editor--disabled': this.readOnly,
                }
            },
            style() {
                return {
                    '--height': `${this.height}px`,
                }
            },
            bubbleMenuIgnoredExtensions() {
                return [
                    Upload,
                    HorizontalRule,
                ]
            },
            hasUpload() {
                return this.editor.options.extensions?.find(extension => extension.name === Upload.name);
            },
        },
        async mounted() {
            await this.$nextTick();
            this.editor.view.dom.classList.add(
                'card-body',
                'form-control',
                'editor__content',
            );
        },
    }
</script>
