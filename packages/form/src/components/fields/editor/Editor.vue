<script setup lang="ts">
    import { __ } from "@/util/i18n";
</script>

<template>
    <div class="editor" :class="classes" :style="style">
        <div class="card">
            <template v-if="editor && toolbar">
                <div class="card-header editor__header" v-sticky ref="header">
                    <MenuBar
                        :id="uniqueIdentifier"
                        :editor="editor"
                        :toolbar="toolbar"
                        :disabled="readOnly"
                        :options="toolbarOptions"
                        :embeds="embeds"
                    />
                </div>
            </template>

            <EditorContent :editor="editor" />

            <template v-if="editor && !readOnly">
                <template v-if="hasUpload">
                    <UploadFileInput :editor="editor"/>
                </template>
            </template>

            <template v-if="editor && showCharacterCount">
                <div class="card-footer fs-8 text-muted bg-white">
                    <template v-if="maxLength">
                        <span :class="{ 'text-danger': characterCount > maxLength }">
                            {{ __('sharp::form.editor.character_count', { count: `${characterCount} / ${maxLength}` }) }}
                        </span>
                    </template>
                    <template v-else>
                        {{ __('sharp::form.editor.character_count', { count: characterCount }) }}
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import { __ } from "@/util/i18n";
    import { Upload } from "./extensions/upload/upload";
    import UploadFileInput from "./extensions/upload/UploadFileInput.vue";
    import MenuBar from "./toolbar/MenuBar.vue";
    import { sticky } from 'sharp/directives';
    import { onLabelClicked } from "../../../util/accessibility";

    export default {
        inheritAttrs: false,
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
            toolbarOptions: Array,
            embeds: Object,
            showCharacterCount: Boolean,
            maxLength: Number,
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
                    'editor--no-toolbar': !this.toolbar,
                }
            },
            style() {
                const headingDepth = [...new Set(this.toolbar?.filter(button => button.startsWith('heading')))].length;
                return {
                    '--min-height': this.minHeight ? `${this.minHeight}px` : null,
                    '--max-height': this.maxHeight ? `${this.maxHeight}px` : null,
                    '--heading-depth': headingDepth,
                }
            },
            hasUpload() {
                return this.editor.options.extensions?.find(extension => extension.name === Upload.name);
            },
            characterCount() {
                return this.editor.storage.characterCount.characters();
            },
        },
        methods: {
            validate() {
                if(this.maxLength && !this.showCharacterCount && this.characterCount > this.maxLength) {
                    return __('sharp::form.text.validation.maxlength', { maxlength: this.maxLength });
                }
                return null;
            },
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

                if(this.toolbar) {
                    const headerRect = this.$refs.header.getBoundingClientRect();
                    if(cursorRect.top < headerRect.bottom) {
                        window.scrollBy(0, cursorRect.top - headerRect.bottom - 10);
                    }
                }
            },
            handleUpdated() {
                const error = this.validate();
                this.$emit('update', { editor: this.editor, error });
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
            this.editor.on('update', debounce(this.handleUpdated, 50));

            onLabelClicked(this, this.id, () => this.focus());
        },
        directives: {
            sticky,
        },
    }
</script>
