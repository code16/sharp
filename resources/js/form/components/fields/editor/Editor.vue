<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { vSticky } from "@/directives/sticky";
    import { FormEditorFieldData } from "@/types";
    import { FormFieldProps } from "@/form/components/types";
    import { computed, nextTick, onMounted, ref, toRef, toRefs, watch } from "vue";
    import { Editor, EditorOptions } from "@tiptap/vue-3";
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import UploadFileInput from "./extensions/upload/UploadFileInput.vue";
    import MenuBar from "./toolbar/MenuBar.vue";
    import { normalizeText } from "@/form/util/text";
    import { useLocalizedEditor } from "@/form/components/fields/editor/useLocalizedEditor";
    import {
        getDefaultExtensions,
        getUploadExtension
    } from "@/form/components/fields/editor/index";
    import { Markdown } from "tiptap-markdown";
    import { config } from "@/utils/config";
    import { Iframe } from "@/form/components/fields/editor/extensions/iframe/iframe";
    import { getEmbedExtension } from "@/form/components/fields/editor/extensions/embed";
    import { useForm } from "@/form/useForm";
    import { trimHTML } from "@/form/components/fields/editor/utils/html";

    const emit = defineEmits();
    const props = defineProps<FormFieldProps<FormEditorFieldData>>();
    const form = useForm();
    const editor = useLocalizedEditor(
        props,
        () => {
            const { field } = props;
            const extensions = [
                ...getDefaultExtensions({
                    placeholder: field.placeholder,
                    toolbar: field.toolbar,
                    inline: field.inline,
                }),
                field.markdown
                    ? Markdown.configure({
                        breaks: config('sharp.markdown_editor.nl2br'),
                    })
                    : null,
                field.embeds.upload
                    ? getUploadExtension(props, {
                        onUpdate: files => emit('input', { ...props.value, files }),
                        entityKey: form.entityKey,
                        instanceId: form.instanceId,
                    })
                    : null,
                ...Object.entries({ ...field.embeds, upload: null })
                    .map(([embedKey, embedOptions]) =>
                        embedOptions && getEmbedExtension({
                            embedKey,
                            embedOptions,
                            entityKey: form.entityKey,
                            instanceId: form.instanceId,
                        }),
                    ),
            ].filter(Boolean);

            const content = field.localized && typeof props.value?.text === 'object'
                ? props.value?.text[props.locale]
                : props.value?.text;

            const editor = new Editor({
                content,
                editable: !field.readOnly,
                enableInputRules: false,
                enablePasteRules: [Iframe],
                extensions,
                injectCSS: false,
            });

            watch(() => props.field.readOnly, readOnly => editor.setEditable(readOnly));

            editor.on('update', debounce(() => {
                const error = validate();
                const content = props.field.markdown
                    ? normalizeText(editor.storage.markdown.getMarkdown() ?? '')
                    : normalizeText(trimHTML(editor.getHTML(), { inline: props.field.inline }));

                if(props.field.localized && typeof props.value.text === 'object') {
                    emit('input', {
                        ...props.value,
                        text: { ...props.value.text, [props.locale]: content }
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
    const header = ref<HTMLElement>();

    function validate() {
        if(this.maxLength
            && !this.showCharacterCount
            && editor.storage.characterCount.characters() > this.maxLength
        ) {
            return __('sharp::form.text.validation.maxlength', { maxlength: this.maxLength });
        }
        return null;
    }


    onMounted(async () => {
        await nextTick();
        editor.view.dom.classList.add(
            'card-body',
            'form-control',
            'editor__content',
        );
    });
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
                        :id="fieldErrorKey"
                        :editor="editor"
                        :toolbar="field.toolbar"
                        :disabled="field.readOnly"
                        :options="toolbarOptions"
                        :embeds="field.embeds"
                        :field="field"
                    />
                </div>
            </template>

            <EditorContent :editor="editor" />

            <template v-if="editor && !field.readOnly">
                <template v-if="field.embeds.upload">
                    <UploadFileInput :editor="editor" />
                </template>
            </template>

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
