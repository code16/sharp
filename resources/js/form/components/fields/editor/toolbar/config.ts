import { __ } from "@/utils/i18n";
import {FormEditorToolbarButton} from "@/types";
import {Editor} from "@tiptap/vue-3";
import {
    ALargeSmall,
    Bold,
    Code,
    Heading1,
    Heading2,
    Heading3,
    Highlighter,
    Italic,
    List,
    ListOrdered,
    Quote,
    ImageIcon,
    Paperclip, Minus, SquarePlay, CodeXml, FileCode, Undo, Redo, TextQuote,
    Superscript,
} from "lucide-vue-next";
import { Component } from "vue";

type ButtonConfig = {
    command: (editor: Editor, data?: any) => void,
    isActive?: (editor: Editor) => boolean,
    icon: Component,
    label?: () => string,
}

export const buttons: { [key in Exclude<FormEditorToolbarButton, '|' | 'link' | 'table'>]: ButtonConfig } = {
    'bold': {
        command: editor => editor.chain().focus().toggleBold().run(),
        isActive: editor => editor.isActive('bold'),
        icon: Bold,
        label: () => __('sharp::form.editor.toolbar.bold.title'),
    },
    'italic': {
        command: editor => editor.chain().focus().toggleItalic().run(),
        isActive: editor => editor.isActive('italic'),
        icon: Italic,
        label: () => __('sharp::form.editor.toolbar.italic.title'),
    },
    'highlight': {
        command: editor => editor.chain().focus().toggleHighlight().run(),
        isActive: editor => editor.isActive('highlight'),
        icon: Highlighter,
        label: () => __('sharp::form.editor.toolbar.highlight.title'),
    },
    'small': {
        command: editor => editor.chain().focus().toggleSmall().run(),
        isActive: editor => editor.isActive('small'),
        icon: ALargeSmall,
        label: () => __('sharp::form.editor.toolbar.small.title'),
    },
    'heading-1': {
        command: editor => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        isActive: editor => editor.isActive('heading', { level: 1 }),
        icon: Heading1,
        label: () => __('sharp::form.editor.toolbar.heading_1.title'),
    },
    'heading-2': {
        command: editor => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        isActive: editor => editor.isActive('heading', { level: 2 }),
        icon: Heading2,
        label: () => __('sharp::form.editor.toolbar.heading_2.title'),
    },
    'heading-3': {
        command: editor => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        isActive: editor => editor.isActive('heading', { level: 3 }),
        icon: Heading3,
        label: () => __('sharp::form.editor.toolbar.heading_3.title'),
    },
    'code': {
        command: editor => editor.chain().focus().toggleCode().run(),
        isActive: editor => editor.isActive('code'),
        icon: Code,
        label: () => __('sharp::form.editor.toolbar.code.title'),
    },
    'blockquote': {
        command: editor => editor.chain().focus().toggleBlockquote().run(),
        isActive: editor => editor.isActive('blockquote'),
        icon: TextQuote,
        label: () => __('sharp::form.editor.toolbar.quote.title'),
    },
    'bullet-list': {
        command: editor => editor.chain().focus().toggleBulletList().run(),
        isActive: editor => editor.isActive('bulletList'),
        icon: List,
        label: () => __('sharp::form.editor.toolbar.unordered_list.title'),
    },
    'ordered-list': {
        command: editor => editor.chain().focus().toggleOrderedList().run(),
        isActive: editor => editor.isActive('orderedList'),
        icon: ListOrdered,
        label: () => __('sharp::form.editor.toolbar.ordered_list.title'),
    },
    'upload-image': {
        command: () => {}, // handled in MenuBar
        isActive: editor => editor.isActive('upload', { isImage: true }),
        icon: ImageIcon,
        label: () => __('sharp::form.editor.toolbar.upload_image.title'),
    },
    'upload': {
        command: () => {}, // handled in MenuBar
        isActive: editor => editor.isActive('upload', { isImage: false }),
        icon: Paperclip,
        label: () => __('sharp::form.editor.toolbar.upload.title'),
    },
    'horizontal-rule': {
        command: editor => editor.chain().focus().setHorizontalRule().run(),
        isActive: editor => editor.isActive('horizontalRule'),
        icon: Minus,
        label: () => __('sharp::form.editor.toolbar.horizontal_rule.title'),
    },
    'iframe': {
        command: editor => editor.chain().focus().insertIframe().run(),
        isActive: editor => editor.isActive('iframe'),
        icon: SquarePlay,
        label: () => __('sharp::form.editor.toolbar.iframe.title'),
    },
    'html': {
        command: editor => editor.chain().focus().insertHtml().run(),
        isActive: editor => editor.isActive('html'),
        icon: CodeXml,
        label: () => __('sharp::form.editor.toolbar.html.title'),
    },
    'code-block': {
        command: editor => editor.chain().focus().toggleCodeBlock().run(),
        isActive: editor => editor.isActive('codeBlock'),
        icon: FileCode,
        label: () => __('sharp::form.editor.toolbar.code_block.title'),
    },
    'superscript': {
        command: editor => editor.chain().focus().toggleSuperscript().run(),
        isActive: editor => editor.isActive('superscript'),
        icon: Superscript,
        label: () => __('sharp::form.editor.toolbar.superscript.title'),
    },
    'undo': {
        command: editor => editor.chain().undo().run(),
        icon: Undo,
        label: () => __('sharp::form.editor.toolbar.undo.title'),
    },
    'redo': {
        command: editor => editor.chain().redo().run(),
        icon: Redo,
        label: () => __('sharp::form.editor.toolbar.redo.title'),
    }
};
