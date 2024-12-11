<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormEditorFieldData } from "@/types";
    import { provide, ref, watch } from "vue";
    import { Editor, BubbleMenu, isActive } from "@tiptap/vue-3";
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import { normalizeText } from "@/form/util/text";
    import { useLocalizedEditor } from "@/form/components/fields/editor/useLocalizedEditor";
    import { Markdown } from "tiptap-markdown";
    import { config } from "@/utils/config";
    import { Iframe } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import { useParentForm } from "@/form/useParentForm";
    import { trimHTML } from "@/form/components/fields/editor/utils/html";
    import { getExtensionsForEditor } from "@/form/components/fields/editor/extensions";
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
    import { Button } from "@/components/ui/button";
    import { buttons } from "@/form/components/fields/editor/toolbar/config";
    import LinkDropdown from "@/form/components/fields/editor/toolbar/LinkDropdown.vue";
    import TableDropdown from "@/form/components/fields/editor/toolbar/TableDropdown.vue";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import Icon from "@/components/ui/Icon.vue";
    import { Separator } from "@/components/ui/separator";
    import { Toggle } from "@/components/ui/toggle";

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
    const linkDropdown = ref<InstanceType<typeof LinkDropdown>>();

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
                ...getExtensionsForEditor(field),
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
        <div class="editor rounded-md border border-input bg-background focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-within:ring-offset-background"
            :class="{
                'editor--disabled': field.readOnly,
                'editor--no-toolbar': !field.toolbar,
            }"
        >
            <template v-if="editor && field.toolbar">
                <StickyTop class="sticky top-[--top-bar-height] [[role=dialog]_&]:top-0 p-1.5 border-b data-[stuck]:z-10 data-[stuck]:bg-background">
                    <div class="flex gap-x-1 gap-y-0 flex-wrap" ref="header">
                        <template v-for="button in props.field.toolbar">
                            <template v-if="button === 'link'">
                                <LinkDropdown v-bind="props" :editor="editor" :ref="(c) => linkDropdown = c as InstanceType<typeof LinkDropdown>" />
                            </template>
                            <template v-else-if="button === 'table'">
                                <TableDropdown v-bind="props" :editor="editor" />
                            </template>
                            <template v-else-if="button.startsWith('embed:')">
                                <Toggle
                                    size="sm"
                                    :model-value="editor.isActive(button)"
                                    :disabled="props.field.readOnly"
                                    :title="props.field.embeds[button.replace('embed:', '')]?.label"
                                    @click="embedModal.open({ embed: props.field.embeds[button.replace('embed:', '')] })"
                                >
                                    <Icon :icon="field.embeds[button.replace('embed:', '')]?.icon" class="size-4" />
                                </Toggle>
                            </template>
                            <template v-else-if="button === '|'">
                                <Separator orientation="vertical" class="h-4 self-center last:hidden" />
                            </template>
                            <template v-else :key="button">
                                <Toggle
                                    size="sm"
                                    :model-value="buttons[button].isActive(editor)"
                                    :disabled="field.readOnly"
                                    :title="buttons[button].label()"
                                    @click="button === 'upload' || button === 'upload-image'
                                        ? uploadModal.open()
                                        : buttons[button].command(editor)"
                                    :data-test="button"
                                >
                                    <component :is="buttons[button].icon" class="size-4" />
                                </Toggle>
                            </template>
                        </template>
                        <template v-if="Object.values(props.field.embeds ?? {}).length > 0">
                            <DropdownMenu :modal="false">
                                <DropdownMenuTrigger as-child>
                                    <Button class="px-3" variant="ghost" :disabled="props.field.readOnly">
                                        {{ __('sharp::form.editor.dropdown.embeds') }}
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <template v-for="embed in props.field.embeds">
                                        <DropdownMenuItem @click="embedModal.open({ embed })">
                                            {{ embed.label }}
                                        </DropdownMenuItem>
                                    </template>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </template>
                    </div>
                </StickyTop>
            </template>

            <EditorAttributes
                :class="cn(
                    'group/editor content min-h-20 w-full rounded-b-md overflow-y-auto focus:outline-none px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50',
                    '[&_.selection-highlight]:bg-[Highlight] [&_.selection-highlight]:py-0.5',
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

            <BubbleMenu
                :editor="editor"
                :should-show="({ state }) => isActive(state, 'link')"
                :key="'bubble-menu-' + (props.locale ?? '')"
            >
                <Button @click="linkDropdown.open()">
                    Update link
                </Button>
            </BubbleMenu>

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
        </div>
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
    </FormFieldLayout>
</template>
