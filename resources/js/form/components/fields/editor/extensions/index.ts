import { Extension, getExtensionField, getSchema } from "@tiptap/core";
import { Document } from '@tiptap/extension-document';
import { Text } from '@tiptap/extension-text';
import { Paragraph } from '@tiptap/extension-paragraph';
import { Blockquote } from '@tiptap/extension-blockquote';
import { Bold } from '@tiptap/extension-bold';
import { History } from '@tiptap/extension-history';
import { Gapcursor } from '@tiptap/extension-gapcursor';
import { Dropcursor } from '@tiptap/extension-dropcursor';
import { BulletList } from '@tiptap/extension-bullet-list';
import { Code } from '@tiptap/extension-code';
import { Heading } from '@tiptap/extension-heading';
import { HardBreak } from '@tiptap/extension-hard-break';
import { Image } from '@tiptap/extension-image';
import { Italic } from '@tiptap/extension-italic';
import { ListItem } from '@tiptap/extension-list-item';
import { HorizontalRule } from '@tiptap/extension-horizontal-rule';
import { Placeholder } from '@tiptap/extension-placeholder';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableHeader } from '@tiptap/extension-table-header';
import { TableCell } from '@tiptap/extension-table-cell';
import { Highlight } from '@tiptap/extension-highlight';
import { CodeBlock } from '@tiptap/extension-code-block';
import { Superscript } from '@tiptap/extension-superscript';
import { OrderedList } from '@tiptap/extension-ordered-list';
import { Link } from '@tiptap/extension-link';
import { Selection } from './Selection';
import { Html } from './html/Html';
import { TrailingNode } from './TrailingNode';
import { Iframe } from './iframe/Iframe';
import { Clipboard } from './Clipboard';
import { Small } from './Small';
import { CharacterCount } from '@tiptap/extension-character-count';
import { FormEditorFieldData, FormEditorToolbarButton } from "@/types";


export function getExtensions(field: FormEditorFieldData) {
    const toolbarHas = (buttonName: FormEditorToolbarButton | FormEditorToolbarButton[]) =>
        !field.toolbar || field.toolbar.some(button =>
            Array.isArray(buttonName)
                ? buttonName.includes(button)
                : button === buttonName
        );

    return [
        toolbarHas('blockquote') && Blockquote,
        toolbarHas('bold') && Bold,
        toolbarHas('bullet-list') && BulletList,
        Extension.create({
            addExtensions() { // use addExtension to ensure unique state
                return [
                    CharacterCount.configure(),
                ]
            }
        }),
        Clipboard.configure({
            inline: field.inline,
        }),
        toolbarHas('code') && Code,
        toolbarHas('code-block') && CodeBlock,
        Document.extend({
            content: field.uploads || Object.keys(field.embeds ?? {}).length
                ? '(block | embed)+'
                : 'block+',
        }),
        Dropcursor,
        Gapcursor,
        HardBreak.extend({
            addKeyboardShortcuts() {
                if(field.inline) {
                    return {
                        'Enter': () => this.editor.commands.setHardBreak(),
                        ...this.parent(),
                    }
                }
                return this.parent();
            },
        }),
        History,
        toolbarHas('highlight') && Highlight,
        toolbarHas([
            'heading-1',
            'heading-2',
            'heading-3'
        ]) && Heading.configure({
            levels: [1,2,3].filter((level: 1|2|3) => toolbarHas(`heading-${level}`)) as (1|2|3)[],
        }),
        toolbarHas('horizontal-rule') && HorizontalRule.extend({
            // selectable: false,
        }),
        Html,
        toolbarHas('iframe') && Iframe,
        toolbarHas('italic') && Italic,
        toolbarHas('link') && Link.extend({
            inclusive: false,
        }).configure({
            openOnClick: false,
            HTMLAttributes: {
                rel: null,
                target: null,
            },
        }),
        toolbarHas(['bullet-list', 'ordered-list']) && ListItem,
        Image,
        toolbarHas('ordered-list') && OrderedList,
        Paragraph,
        field.placeholder && Placeholder.configure({
            placeholder: field.placeholder,
        }),
        Selection,
        toolbarHas('small') && Small,
        toolbarHas('superscript') && Superscript,
        toolbarHas('table') && [
            Table,
            TableRow,
            TableHeader,
            TableCell,
        ],
        Text,
        !field.inline && TrailingNode,
    ]
        .flat()
        .filter(extension => !!extension);
}
