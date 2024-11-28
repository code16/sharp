<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormEditorFieldData } from "@/types";
    import { provide, ref, useTemplateRef, watch } from "vue";
    import { Editor } from "@tiptap/vue-3";
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import Toolbar from "./toolbar/Toolbar.vue";
    import { normalizeText } from "@/form/util/text";
    import { useLocalizedEditor } from "@/form/components/fields/editor/useLocalizedEditor";
    import { Markdown } from "tiptap-markdown";
    import { config } from "@/utils/config";
    import { Iframe } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import { useParentForm } from "@/form/useParentForm";
    import { trimHTML } from "@/form/components/fields/editor/utils/html";
    import { getDefaultExtensions } from "@/form/components/fields/editor/extensions";
    import { ContentEmbedManager } from "@/content/ContentEmbedManager";
    import { ContentUploadManager } from "@/content/ContentUploadManager";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";
    import { ParentEditor } from "@/form/components/fields/editor/useParentEditor";
    import { Upload } from "@/form/components/fields/editor/extensions/upload/Upload";
    import { Embed } from "@/form/components/fields/editor/extensions/embed/Embed";
    import EditorUploadModal from "@/form/components/fields/editor/extensions/upload/EditorUploadModal.vue";
    import EditorEmbedModal from "@/form/components/fields/editor/extensions/embed/EditorEmbedModal.vue";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import EditorAttributes from "@/form/components/fields/editor/EditorAttributes.vue";
    import { cn } from "@/utils/cn";
    import StickyTop from "@/components/StickyTop.vue";

    const emit = defineEmits<FormFieldEmits<FormEditorFieldData>>();
    const props = defineProps<FormFieldProps<FormEditorFieldData>>();

    const header = ref<HTMLElement>();
    const form = useParentForm();

    const uploadManager = new ContentUploadManager(form, props.value?.uploads, {
        editorField: props.field,
        onUploadsUpdated(uploads) {
            emit('input', { ...props.value, uploads });
        }
    });
    const uploadModal = ref<InstanceType<typeof EditorUploadModal>>();

    const embedManager = new ContentEmbedManager(form, props.field.embeds, props.value?.embeds, {
        onEmbedsUpdated(embeds) {
            emit('input', { ...props.value, embeds });
        }
    });
    const embedModal = ref<InstanceType<typeof EditorEmbedModal>>();

    provide<ParentEditor>('editor', {
        props,
        uploadManager,
        uploadModal,
        embedManager,
        embedModal,
    } satisfies ParentEditor);

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
                content: props.field.localized && typeof props.value?.text === 'object'
                    ? props.value?.text?.[locale] ?? ''
                    : props.value?.text ?? '',
                editable: !field.readOnly,
                enableInputRules: false,
                enablePasteRules: [Iframe],
                extensions,
                injectCSS: false,
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

                if(props.field.localized && typeof props.value?.text === 'object') {
                    emit('input', {
                        ...props.value,
                        text: { ...props.value.text, [locale]: content },
                    }, { error });
                } else {
                    emit('input', { ...props.value, text: content }, { error });
                }
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
    <FormFieldLayout v-bind="props">
        <div class="editor"
            :class="{
                'editor--disabled': field.readOnly,
                'editor--no-toolbar': !field.toolbar,
            }"
        >
            <template v-if="editor && field.toolbar">
                <StickyTop class="sticky top-[--top-bar-height] [[role=dialog]_&]:top-0 data-[stuck]:border-b data-[stuck]:z-10 bg-background">
                    <div ref="header">
                        <Toolbar
                            :editor="editor"
                            v-bind="props"
                            @upload="uploadModal.open()"
                            @embed="(embed) => embedModal.open({ embed })"
                        />
                    </div>
                </StickyTop>
            </template>

            <EditorAttributes
                :class="cn(
                    'group/editor content min-h-20 w-full rounded-md border border-input overflow-y-auto bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    {
                        'min-h-[--min-height]': field.minHeight,
                        'max-h-[--max-height]': field.maxHeight,
                    },
                )"
                :style="{
                    '--min-height': field.minHeight ? `${field.minHeight}px` : null,
                    '--max-height': field.maxHeight ? `${field.maxHeight}px` : null,
                }"
                :editor="editor"
            >
                <EditorContent :editor="editor" :key="locale ?? 'editor'" />
            </EditorAttributes>

            <template v-if="props.field.uploads">
                <EditorUploadModal
                    :field="field"
                    :editor="editor"
                    ref="uploadModal"
                />
            </template>

            <template v-if="props.field.embeds">
                <EditorEmbedModal
                    :editor="editor"
                    :field="field"
                    ref="embedModal"
                />
            </template>

            <template v-if="editor && field.showCharacterCount">
                <div class="mt-2 text-xs text-muted-foreground">
                    <template v-if="field.maxLength">
                        <div :class="{ 'text-destructive': editor.storage.characterCount.characters() > field.maxLength }">
                            {{ __('sharp::form.editor.character_count', { count: `${editor.storage.characterCount.characters()} / ${field.maxLength}` }) }}
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            {{ __('sharp::form.editor.character_count', { count: editor.storage.characterCount.characters() }) }}
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </FormFieldLayout>
</template>
