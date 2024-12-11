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
import { Selected } from './Selected';
import { Html } from './html/Html';
import { TrailingNode } from './TrailingNode';
import { Iframe } from './iframe/Iframe';
import { Link } from './link/Link';
import { Paste } from './Paste';
import { Small } from './Small';
import { CharacterCount } from '@tiptap/extension-character-count';
import { getAllowedHeadingLevels, toolbarHasButton } from "../utils";
import { FormEditorFieldData } from "@/types";


export function getExtensionsForEditorField(field: FormEditorFieldData) {
    const getExtensions = (field: FormEditorFieldData) => [
        toolbarHasButton(field, 'blockquote') && [
            Blockquote,
        ],
        toolbarHasButton(field, 'bold') && [
            Bold,
        ],
        toolbarHasButton(field, 'bullet-list') && [
            BulletList,
        ],
        Extension.create({
            addExtensions() { // use addExtension to ensure unique state
                return [
                    CharacterCount.configure(),
                ]
            }
        }),
        toolbarHasButton(field, 'code') && [
            Code,
        ],
        toolbarHasButton(field, 'code-block') && [
            CodeBlock,
        ],
        Document,
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
        toolbarHasButton(field, 'highlight') && [
            Highlight,
        ],
        toolbarHasButton(field, ['heading-1', 'heading-2', 'heading-3']) && [
            Heading.configure({
                levels: field.toolbar ? getAllowedHeadingLevels(field.toolbar) : [1,2,3],
            }),
        ],
        toolbarHasButton(field, 'horizontal-rule') && [
            HorizontalRule.extend({
                selectable: false,
            }),
        ],
        Html,
        toolbarHasButton(field, 'iframe') && [
            Iframe,
        ],
        toolbarHasButton(field, 'italic') && [
            Italic,
        ],
        toolbarHasButton(field, 'link') && [
            Link.configure({
                openOnClick: false,
                HTMLAttributes: {
                    rel: null,
                    target: null,
                },
            }),
        ],
        toolbarHasButton(field, ['bullet-list', 'ordered-list']) && [
            ListItem,
        ],
        Image.configure({
            HTMLAttributes: {
                class: 'editor__image',
            },
        }),
        toolbarHasButton(field, 'ordered-list') && [
            OrderedList,
        ],
        Paragraph,
        field.placeholder && [
            Placeholder.configure({
                placeholder: field.placeholder,
            })
        ],
        Selected,
        toolbarHasButton(field, 'small') && [
            Small,
        ],
        toolbarHasButton(field, 'superscript') && [
            Superscript,
        ],
        toolbarHasButton(field, 'table') && [
            Table,
            TableRow,
            TableHeader,
            TableCell,
        ],
        Text,
        !field.inline && [
            TrailingNode,
        ],
    ]
        .flat()
        .filter(extension => !!extension);

    return [
        ...getExtensions(field),
        Paste.configure({
            schema: getSchema(getExtensions({
                ...field,
                toolbar: field.toolbar ?? [], // if no toolbar, prevent pasting formatted HTML
            })),
            inline: field.inline,
        }),
    ];
}
