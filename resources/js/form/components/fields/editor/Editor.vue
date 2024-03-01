<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { vSticky } from "@/directives/sticky";
    import { FormEditorFieldData } from "@/types";
    import { provide, ref, watch } from "vue";
    import { Editor } from "@tiptap/vue-3";
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import MenuBar from "./toolbar/MenuBar.vue";
    import { normalizeText } from "@/form/util/text";
    import { useLocalizedEditor } from "@/form/components/fields/editor/useLocalizedEditor";
    import { Markdown } from "tiptap-markdown";
    import { config } from "@/utils/config";
    import { Iframe } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import { useParentForm } from "@/form/useParentForm";
    import { trimHTML } from "@/form/components/fields/editor/utils/html";
    import { getDefaultExtensions } from "@/form/components/fields/editor/extensions";
    import { useEditorEmbeds } from "@/form/components/fields/editor/extensions/embed/useEditorEmbeds";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { useEditorUploads } from "@/form/components/fields/editor/extensions/upload/useEditorUploads";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { Serializable } from "@/form/Serializable";
    import { FormFieldProps } from "@/form/types";
    import { ParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";
    import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";

    const emit = defineEmits(['input']);
    const props = defineProps<
        FormFieldProps<FormEditorFieldData>
    >();

    const header = ref<HTMLElement>();
    const form = useParentForm();

    const uploadManager = new ContentUploadManager(form, {
        editorField: props.field,
        onFilesUpdated(files) {
            emit('input', { ...props.value, files });
        }
    });
    const embedManager = new ContentEmbedManager(form, props.field.embeds, {
        onEmbedsUpdated(embeds) {
            emit('input', { ...props.value, embeds });
        }
    });
    const initialFormattedContent = (
        uploadManager.withUploadsUniqueId(
            embedManager.withEmbedsUniqueId(
                props.value?.text
            )
        )
    );

    uploadManager.resolveContentUploads(initialFormattedContent);
    embedManager.resolveContentEmbeds(initialFormattedContent);

    provide<ParentEditor>('editor', {
        props,
        embedManager,
        uploadManager,
    });

    const editor = useLocalizedEditor(
        props,
        (locale) => {
            const { field } = props;
            const extensions = [
                ...getDefaultExtensions({
                    placeholder: field.placeholder,
                    toolbar: field.toolbar,
                    inline: field.inline,
                }),
                field.markdown && Markdown.configure({
                    breaks: config('sharp.markdown_editor.nl2br'),
                }),
                props.field.uploads && Upload.configure({
                    uploadManager,
                }),
                ...Object.values(props.field.embeds ?? {})
                    .map((embed) => {
                        return Embed.extend({
                            name: `embed:${embed.key}`,
                            addOptions() {
                                return { embed, embedManager }
                            }
                        })
                    }),
            ].filter(Boolean);

            const editor = new Editor({
                content: props.field.localized && typeof initialFormattedContent === 'object'
                    ? initialFormattedContent?.[locale] ?? ''
                    : initialFormattedContent ?? '',
                editable: !field.readOnly,
                enableInputRules: false,
                enablePasteRules: [Iframe],
                extensions,
                injectCSS: false,
                editorProps: {
                    attributes: {
                        class: 'card-body editor__content form-control p-2',
                    },
                }
            });

            watch(() => props.field.readOnly, readOnly => editor.setEditable(readOnly));

            // todo replace with laravel precognition ?
            function validate() {
                if(props.field.maxLength
                    && !props.field.showCharacterCount
                    && editor.storage.characterCount.characters() > props.field.maxLength
                ) {
                    return __('sharp::form.text.validation.maxlength', { maxlength: props.field.maxLength });
                }
                return null;
            }

            editor.on('update', debounce(() => {
                const error = validate();
                const content = props.field.markdown
                    ? normalizeText(editor.storage.markdown.getMarkdown() ?? '')
                    : normalizeText(trimHTML(editor.getHTML(), { inline: props.field.inline }));

                const value = new Serializable(
                    content,
                    embedManager.serializeContent(uploadManager.serializeContent(content)),
                    content => {
                        if(props.field.localized && typeof (props.value.text ?? {}) === 'object') {
                            return {
                                ...props.value,
                                text: { ...props.value.text, [locale]: content }
                            }
                        }
                        return { ...props.value, text: content };
                    }
                );

                emit('input', value, { error });
            }, 50));

            editor.on('selectionUpdate', () => {
                const { from, to } = editor.state.selection;
                const pos = Math.min(from, to);
                const cursorRect = editor.view.coordsAtPos(pos);

                if(props.field.toolbar) {
                    const headerRect = header.value?.getBoundingClientRect();
                    if(cursorRect.top < headerRect.bottom) {
                        window.scrollBy(0, cursorRect.top - headerRect.bottom - 10);
                    }
                }
            })

            return editor;
        }
    );
</script>

<template>
    <div class="editor"
        :class="{
            'editor--disabled': field.readOnly,
            'editor--no-toolbar': !field.toolbar,
        }"
        :style="{
            '--min-height': field.minHeight ? `${field.minHeight}px` : null,
            '--max-height': field.maxHeight ? `${field.maxHeight}px` : null,
        }"
    >
        <div class="card">
            <template v-if="editor && field.toolbar">
                <div class="card-header editor__header" v-sticky ref="header">
                    <MenuBar
                        :editor="editor"
                        v-bind="$props"
                    />
                </div>
            </template>

            <EditorContent :editor="editor" :key="locale ?? 'editor'" />

            <template v-if="editor && field.showCharacterCount">
                <div class="card-footer fs-8 text-muted bg-white">
                    <template v-if="field.maxLength">
                        <span :class="{ 'text-danger': editor.storage.characterCount.characters() > field.maxLength }">
                            {{ __('sharp::form.editor.character_count', { count: `${editor.storage.characterCount.characters()} / ${field.maxLength}` }) }}
                        </span>
                    </template>
                    <template v-else>
                        {{ __('sharp::form.editor.character_count', { count: editor.storage.characterCount.characters() }) }}
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>
