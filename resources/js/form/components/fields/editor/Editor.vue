<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FormEditorFieldData } from "@/types";
    import {
        computed,
        nextTick,
        onBeforeUnmount,
        onMounted,
        provide,
        ref,
        useTemplateRef,
        watch,
        watchEffect
    } from "vue";
    import { Editor,  isActive } from "@tiptap/vue-3";
    import debounce from 'lodash/debounce';
    import { EditorContent } from '@tiptap/vue-3';
    import { normalizeText } from "@/form/util/text";
    import { useLocalizedEditor } from "@/form/components/fields/editor/useLocalizedEditor";
    import { Markdown } from "tiptap-markdown";
    import { config } from "@/utils/config";
    import { Iframe } from "@/form/components/fields/editor/extensions/iframe/Iframe";
    import { useParentForm } from "@/form/useParentForm";
    import { trimHTML } from "@/form/components/fields/editor/utils/html";
    import { getExtensions } from "@/form/components/fields/editor/extensions";
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
    import { Label } from "@/components/ui/label";
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
    import { GripVertical, Maximize2, Minimize2 } from "lucide-vue-next";
    import { useElementSize, useEventListener } from "@vueuse/core";
    import FormFieldLocaleSelect from "@/form/components/FormFieldLocaleSelect.vue";
    import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";
    import { vScrollIntoView } from "@/directives/scroll-into-view";
    import EditorHelpText from "@/form/components/fields/editor/EditorHelpText.vue";
    import FormFieldError from "@/form/components/FormFieldError.vue";
    import EditorMaybeFullscreenDialog from "@/form/components/fields/editor/EditorMaybeFullscreenDialog.vue";
    import { DecorateHiddenCharacters } from "@/form/components/fields/editor/extensions/DecorateHiddenCharacters";
    import { TrailingNode, UndoRedo } from "@tiptap/extensions";
    import { Extension, textInputRule } from "@tiptap/core";

    const emit = defineEmits<FormFieldEmits<FormEditorFieldData>>();
    const props = defineProps<FormFieldProps<FormEditorFieldData>>();

    const header = ref<HTMLElement>();
    const form = useParentForm();

    const uploadManager = new ContentUploadManager(form, props.value?.uploads, {
        editorField: props.field,
    });
    const uploadModal = ref<InstanceType<typeof EditorUploadModal>>();

    const embedManager = new ContentEmbedManager(form, props.field.embeds, props.value?.embeds);
    const el = useTemplateRef<HTMLDialogElement | HTMLDivElement>('el');
    const embedModal = ref<InstanceType<typeof EditorEmbedModal>>();
    const linkDropdown = ref<InstanceType<typeof LinkDropdown>>();

    provide<ParentEditor>('editor', {
        props,
        uploadManager,
        uploadModal,
        embedManager,
        embedModal,
    } satisfies ParentEditor);

    watch(() => [embedManager.contentEmbeds, uploadManager.contentUploads], () => {
        emit('input', {
            ...props.value,
            uploads: uploadManager.serializedUploads,
            embeds: embedManager.serializedEmbeds,
        });
    }, {
        deep: true,
    });

    const editor = useLocalizedEditor(
        props,
        (locale) => {
            const { field } = props;
            const extensions = [
                ...getExtensions(field),
                field.markdown && Markdown.configure({
                    breaks: config('sharp.markdown_editor.nl2br'),
                }),
                props.field.uploads && Upload.configure({
                    uploadManager,
                    locale,
                }),
                Extension.create({
                    name: 'textInputReplacements',
                    addInputRules() {
                        return field.textInputReplacements
                            .filter(replacement => !replacement.locale || replacement.locale === locale)
                            .map(replacement => textInputRule({
                                find: new RegExp(replacement.pattern.replace(/^\//, '').replace(/\/$/, '').replace(/\$?$/, '$')),
                                replace: replacement.replacement,
                            }));
                    },
                }),
                DecorateHiddenCharacters.configure({
                    class: cn(
                        `relative pl-[.125em] after:block after:absolute after:top-1/2 after:-translate-y-1/2 after:left-1/2 after:-translate-x-1/2 after:opacity-25`,
                        `data-[key=nbsp]:after:content-['°']`,
                    ),
                }),
                ...Object.values(props.field.embeds ?? {})
                    .map((embed) => {
                        return Embed.extend({
                            name: `embed:${embed.key}`,
                            addOptions() {
                                return { embed, embedManager, locale }
                            },
                        })
                    }),
            ].filter(Boolean);

            const editor = new Editor({
                content: props.field.localized && typeof props.value?.text === 'object'
                    ? props.value?.text?.[locale] ?? ''
                    : props.value?.text ?? '',
                editable: !field.readOnly,
                enableInputRules: ['textInputReplacements'],
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
                    ? normalizeText((editor.storage as any).markdown.getMarkdown() ?? '')
                    : normalizeText(trimHTML(editor.getHTML(), { inline: props.field.inline }));

                if(props.field.localized) {
                    emit('input', {
                        ...props.value,
                        text: typeof props.value?.text === 'object'
                            ? { ...props.value.text, [locale]: content }
                            : { [locale]: content },
                    }, { error });
                } else {
                    emit('input', { ...props.value, text: content }, { error });
                }
            }, 50));

            editor.on('focus', () => {
                document.documentElement.style.scrollPaddingTop = '200px';
            });

            editor.on('blur', () => {
                document.documentElement.style.scrollPaddingTop = '';
            });

            return editor;
        }
    );

    declare module '@tiptap/core' {
        interface Storage {
            scrollYPercentage: number;
        }
    }

    const isFullscreen = ref(false);
    const fullscreenPlaceholderHeight = ref(0);
    const editorScroller = useTemplateRef('editorScroller');
    const editorScrollerSize = useElementSize(editorScroller);
    function onEditorScroll(e) {
        editor.value.storage.scrollYPercentage = e.target.scrollTop / e.target.scrollHeight;
    }
    watch([editor, isFullscreen], async () => {
        await nextTick();
        await nextTick();
        const scrollY = editorScroller.value.scrollHeight * (editor.value.storage.scrollYPercentage || 0);
        editorScroller.value?.scrollTo(0, scrollY);
    });

    watch(isFullscreen, () => {
        if(isFullscreen.value) {
            fullscreenPlaceholderHeight.value = el.value.offsetHeight;
        }
        setTimeout(() => editor.value.commands.focus());
    });

    async function onLocaleChange(locale: string, focus?: boolean) {
        emit('locale-change', locale);
        if(focus) {
            setTimeout(() => {
                editor.value.commands.focus();
            });
        }
    }

    function onLocaleSelectCloseAutoFocus() {
        setTimeout(() => {
            editor.value.commands.focus();
        });
    }

    const dropdownEmbeds = computed(() =>
        Object.values(props.field.embeds ?? {})
            .filter(embed => !props.field.toolbar?.includes(`embed:${embed.key}`))
    );
</script>

<template>
    <FormFieldLayout
        v-bind="props"
        @locale-change="onLocaleChange"
        @locale-select:close-auto-focus.prevent="onLocaleSelectCloseAutoFocus"
        field-group
    >
        <div v-show="isFullscreen"
            class="h-(--height)"
            :style="{'--height':`${fullscreenPlaceholderHeight}px`}"
        ></div>

        <EditorMaybeFullscreenDialog v-bind="props" v-model:fullscreen="isFullscreen">
            <div
                :class="cn(
                'editor flex flex-col rounded-md overflow-clip border border-input bg-background',
                    isFullscreen
                        ? 'border-none rounded-none bg-transparent'
                        : 'focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2 focus-within:ring-offset-background'
                )"
                :data-fullscreen="isFullscreen ? true : null"
                ref="el"
            >
                <template v-if="editor && (field.toolbar || isFullscreen)">
                    <StickyTop
                        class="shrink-0 sticky top-(--stacked-top) in-data-fullscreen:top-0 in-[[role=dialog]]:top-0 p-1.5 border-b data-stuck:z-10 data-[stuck]:bg-background"
                    >
                        <div class="flex flex-wrap sm:flex-nowrap gap-x-1 sm:in-data-fullscreen:gap-0">
                            <template v-if="isFullscreen">
                                <div class="flex-1 h-9 flex items-center min-w-20">
                                    <div class="flex-1 min-w-0">
                                        <Label as="div" class="ml-2 block  truncate">{{ field.label }}</Label>
                                    </div>
                                </div>
                            </template>
                            <div :class="cn('flex-1 flex gap-x-1 gap-y-0 flex-wrap', {
                                'flex-auto order-1 w-full sm:order-none sm:max-w-3xl sm:px-3.5': isFullscreen,
                            })"
                                ref="header"
                            >
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
                                            @click="embedModal.open({ embed: props.field.embeds[button.replace('embed:', '')], locale: props.locale })"
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
                                            ? uploadModal.open({ locale: props.locale })
                                            : buttons[button].command(editor)"
                                        >
                                            <component :is="buttons[button].icon" class="size-4" />
                                        </Toggle>
                                    </template>
                                </template>
                                <template v-if="dropdownEmbeds.length > 0">
                                    <DropdownMenu :modal="false">
                                        <DropdownMenuTrigger as-child>
                                            <Button class="px-3" variant="ghost" size="sm" :disabled="props.field.readOnly">
                                                {{ __('sharp::form.editor.dropdown.embeds') }}
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent>
                                            <template v-for="embed in dropdownEmbeds">
                                                <DropdownMenuItem @click="embedModal.open({ embed, locale: props.locale })">
                                                    {{ embed.label }}
                                                </DropdownMenuItem>
                                            </template>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </template>
                            </div>
                            <template v-if="isFullscreen">
                                <div class="shrink-0 overflow-y-auto [scrollbar-gutter:stable]"></div>
                            </template>
                            <template v-if="field.allowFullscreen">
                                <div class="h-9 flex gap-x-1 justify-end items-center sm:in-data-fullscreen:flex-1">
                                    <template v-if="field.localized && isFullscreen">
                                        <div class="lg:hidden">
                                            <FormFieldLocaleSelect
                                                v-bind="props"
                                                @locale-change="onLocaleChange"
                                                @close-auto-focus.prevent="onLocaleSelectCloseAutoFocus"
                                            />
                                        </div>
                                    </template>
                                    <Separator orientation="vertical" class="h-4" />
                                    <Toggle v-model="isFullscreen" size="sm">
                                        <template v-if="isFullscreen">
                                            <Minimize2 class="size-4" />
                                        </template>
                                        <template v-else>
                                            <Maximize2 class="size-4" />
                                        </template>
                                    </Toggle>
                                </div>
                            </template>
                        </div>
                    </StickyTop>
                </template>

                <div :class="cn(
                    'flex-1 grid grid-cols-1 overflow-y-auto overflow-x-clip',
                        isFullscreen
                            ? 'lg:[scrollbar-gutter:stable]'
                            : ['min-h-20', {
                                'min-h-(--min-height)': field.minHeight,
                                'max-h-(--max-height)': field.maxHeight,
                            }]
                    )"
                    :style="{
                        '--min-height': field.minHeight ? `${field.minHeight}px` : null,
                        '--max-height': field.maxHeight ? `${field.maxHeight}px` : null,
                        '--height': `${editorScrollerSize.height.value}px`,
                    }"
                    @scroll="onEditorScroll"
                    ref="editorScroller"
                >
                    <div class="relative grid grid-cols-1">
                        <template v-if="isFullscreen && field.localized">
                            <div class="absolute inset-0 hidden lg:flex items-start pointer-events-none">
                                <div class="sticky top-0 p-1 overflow-y-auto overflow-x-clip max-h-(--height) pointer-events-auto">
                                    <Tabs :model-value="locale" @update:model-value="onLocaleChange($event as string, true)">
                                        <TabsList class="flex flex-col items-stretch h-auto">
                                            <template v-for="formLocale in form.locales">
                                                <TabsTrigger :value="formLocale"
                                                    class="group/tab relative shrink-0 uppercase px-5 text-xs text-start scroll-my-1 first:scroll-mt-2 last:scroll-mb-2"
                                                    v-scroll-into-view.nearest="locale === formLocale"
                                                >
                                                    <div class="flex-1">
                                                        {{ formLocale }}
                                                    </div>
                                                    <template v-if="form.fieldHasError(field, fieldErrorKey, formLocale)">
                                                        <svg class="absolute top-1/2 right-2.5 -translate-y-1/2 translate-x-1/2 size-2 fill-destructive" viewBox="0 0 8 8" aria-hidden="true">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                    </template>
                                                </TabsTrigger>
                                            </template>
                                        </TabsList>
                                    </Tabs>
                                </div>
                            </div>
                        </template>

                        <EditorContent
                            class="grid grid-cols-1"
                            :editor="editor"
                            :key="locale ?? 'editor'"
                            @keydown.esc="isFullscreen = false"
                        />

                        <EditorAttributes
                            :editor="editor"
                            :class="cn(
                                'group/editor content w-full rounded-b-md focus:outline-none px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50',
                                '[&_.selection-highlight]:bg-[Highlight] [&_.selection-highlight]:py-0.5',
                                '[&_.ProseMirror-selectednode]:outline-none! [&:focus_.ProseMirror-selectednode]:ring-1 [&_.ProseMirror-selectednode]:ring-primary',
                                {
                                    'content-lg max-w-3xl mx-auto py-6 px-4 sm:px-6 text-base min-h-max': isFullscreen,
                                },
                            )"
                            role="textbox"
                        />
                    </div>
                </div>
                <template v-if="editor && isFullscreen && (field.showCharacterCount || form.fieldHasError(field, fieldErrorKey))">
                    <div class="shrink-0 flex flex-wrap items-center gap-y-1.5 gap-x-2 lg:gap-x-0 py-2.5 px-3 overflow-y-auto lg:[scrollbar-gutter:stable] border-border border-t">
                        <div class="flex-1">
                            <template v-if="editor && field.showCharacterCount">
                                <EditorHelpText class="text-xs/3 text-muted-foreground truncate" v-bind="props" :editor="editor" />
                            </template>
                        </div>
                        <div class="lg:w-full max-w-3xl lg:px-6">
                            <template v-if="form.fieldHasError(field, fieldErrorKey)">
                                <div class="text-sm/4 text-destructive">
                                    <FormFieldError v-bind="props" />
                                </div>
                            </template>
                        </div>
                        <div class="flex-1 hidden lg:block"></div>
                    </div>
                </template>
            </div>

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
        </EditorMaybeFullscreenDialog>

        <template v-if="editor && field.showCharacterCount">
            <div class="mt-2 text-xs text-muted-foreground">
                <EditorHelpText v-bind="props" :editor="editor" />
            </div>
        </template>
    </FormFieldLayout>
</template>
