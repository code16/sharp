<template>
    <div class="editor" :class="classes" :style="style">
        <div class="card">
            <div class="card-header editor__header" v-sticky ref="header">
                <template v-if="editor">
                    <MenuBar
                        :id="uniqueIdentifier"
                        :editor="editor"
                        :toolbar="toolbar"
                        :disabled="readOnly"
                    />
                </template>
            </div>

            <EditorContent :editor="editor" />

            <template v-if="editor && !readOnly">
                <template v-if="hasUpload">
                    <UploadFileInput :editor="editor"/>
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import { EditorContent } from '@tiptap/vue-2';
    import { Upload } from "./extensions/upload/upload";
    import UploadFileInput from "./extensions/upload/UploadFileInput";
    import MenuBar from "./toolbar/MenuBar";
    import { sticky } from 'sharp/directives';
    import { onLabelClicked } from "../../../util/accessibility";

    export default {
        components: {
            EditorContent,
            MenuBar,
            UploadFileInput,
        },
        props: {
            id: String,
            editor: Object,
            uniqueIdentifier: String,
            toolbar: Array,
            minHeight: {
                type: Number,
                default: 300
            },
            maxHeight: Number,
            readOnly: Boolean,
        },
        data() {
            return {
                firstFocus: true,
            }
        },
        watch: {
            readOnly() {
                this.editor.setEditable(!this.readOnly);
            },
        },
        computed: {
            classes() {
                return {
                    'editor--disabled': this.readOnly,
                }
            },
            style() {
                return {
                    '--min-height': this.minHeight ? `${this.minHeight}px` : null,
                    '--max-height': this.maxHeight ? `${this.maxHeight}px` : null,
                }
            },
            hasUpload() {
                return this.editor.options.extensions?.find(extension => extension.name === Upload.name);
            },
        },
        methods: {
            handleFocus() {
                this.firstFocus = false;
            },
            focus() {
                const position = this.firstFocus ? 'end' : null;
                this.editor.commands.focus(position);
            },
            handleSelectionUpdated() {
                const { from, to } = this.editor.state.selection;
                const pos = Math.min(from, to);
                const cursorRect = this.editor.view.coordsAtPos(pos);
                const headerRect = this.$refs.header.getBoundingClientRect();
                if(cursorRect.top < headerRect.bottom) {
                    window.scrollBy(0, cursorRect.top - headerRect.bottom - 10);
                }
            },
        },
        async mounted() {
            await this.$nextTick();
            this.editor.view.dom.classList.add(
                'card-body',
                'form-control',
                'editor__content',
            );
            this.editor.on('focus', this.handleFocus);
            this.editor.on('selectionUpdate', this.handleSelectionUpdated);
            onLabelClicked(this, this.id, () => this.focus());
        },
        directives: {
            sticky,
        },
    }
</script>
