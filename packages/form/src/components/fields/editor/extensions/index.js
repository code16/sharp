import { getExtensionField, getSchema } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import { Heading } from "@tiptap/extension-heading";
import { HardBreak } from "@tiptap/extension-hard-break";
import { Link } from "@tiptap/extension-link";
import { Image } from "@tiptap/extension-image";
import { HorizontalRule } from "@tiptap/extension-horizontal-rule";
import { Placeholder } from "@tiptap/extension-placeholder";
import { Table } from "@tiptap/extension-table";
import { TableRow } from "@tiptap/extension-table-row";
import { TableHeader } from "@tiptap/extension-table-header";
import { TableCell } from "@tiptap/extension-table-cell";
import { Highlight } from "@tiptap/extension-highlight";
import { CodeBlock } from "@tiptap/extension-code-block";
import { Superscript } from "@tiptap/extension-superscript";
import { Selected } from "./selected";
import { Html } from "./html";
import { TrailingNode } from "./trailing-node";
import { Iframe } from "./iframe";
import { Paste } from "./paste";
import { Small } from "./small";
import { getAllowedHeadingLevels, toolbarHasButton } from "../util";


function getHeadingExtension(toolbar) {
    if(!toolbar) {
        return Heading;
    }
    const levels = getAllowedHeadingLevels(toolbar);
    if(levels.length > 0) {
        return Heading.configure({
            levels,
        });
    }
}

function getLinkExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'link')) {
        return Link.configure({
            openOnClick: false,
        });
    }
}

function getImageExtension(toolbar) {
    return Image.configure({
        HTMLAttributes: {
            class: 'editor__image',
        },
    });
}

function getHorizontalRuleExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'horizontal-rule')) {
        return HorizontalRule.extend({
            selectable: false,
        });
    }
}

function getTableExtensions(toolbar) {
    if(toolbarHasButton(toolbar, 'table')) {
        return [
            Table,
            TableRow,
            TableHeader,
            TableCell,
        ];
    }
}

function getPlaceholderExtension(placeholder) {
    if(placeholder) {
        return Placeholder.configure({
            placeholder,
        });
    }
}

function getIframeExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'iframe')) {
        return Iframe;
    }
}

function getHighlightExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'highlight')) {
        return Highlight;
    }
}

function getSmallExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'small')) {
        return Small;
    }
}

function getCodeBlockExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'code-block')) {
        return CodeBlock;
    }
}

function getSuperscriptExtension(toolbar) {
    if(toolbarHasButton(toolbar, 'superscript')) {
        return Superscript;
    }
}

function getPasteExtension({ toolbar, inline }) {
    const extensions = getToolbarExtensions({ toolbar, inline });
    const schema = getSchema(extensions);
    return Paste.configure({
        schema,
        inline,
    });
}

function getHardBreakExtension({ inline }) {
    return HardBreak
        .extend({
            addKeyboardShortcuts() {
                if(inline) {
                    return {
                        'Enter': () => this.editor.commands.setHardBreak(),
                        ...this.parent(),
                    }
                }
                return this.parent();
            },
        });
}

function getTrailingNodeExtension({ inline }) {
    if(!inline) {
        return TrailingNode;
    }
}

function getStarterKitExtensions(toolbar) {
    const bulletList = toolbarHasButton(toolbar, 'bullet-list');
    const orderedList = toolbarHasButton(toolbar, 'ordered-list');
    const starterKit = StarterKit.configure({
        blockquote: toolbarHasButton(toolbar, 'blockquote'),
        bold: toolbarHasButton(toolbar, 'bold'),
        bulletList,
        code: toolbarHasButton(toolbar, 'code'),
        codeBlock: false,
        document: true,
        dropcursor: true,
        gapcursor: true,
        hardBreak: false,
        heading: false,
        history: true,
        horizontalRule: false,
        italic: toolbarHasButton(toolbar, 'italic'),
        listItem: bulletList || orderedList,
        orderedList,
        paragraph: true,
        strike: false,
        text: true,
    });
    return getExtensionField(starterKit, 'addExtensions', starterKit)();
}

function getToolbarExtensions({ toolbar, inline }) {
    const extensions = [
        getStarterKitExtensions(toolbar),
        getHeadingExtension(toolbar),
        getLinkExtension(toolbar),
        getImageExtension(toolbar),
        getHorizontalRuleExtension(toolbar),
        getTableExtensions(toolbar),
        getHighlightExtension(toolbar),
        getSmallExtension(toolbar),
        getIframeExtension(toolbar),
        getCodeBlockExtension(toolbar),
        getSuperscriptExtension(toolbar),
        getHardBreakExtension({ inline }),
    ];
    return extensions
        .flat()
        .filter(extension => !!extension);
}

export function getDefaultExtensions({ placeholder, toolbar, inline } = {}) {
    const extensions = [
        getToolbarExtensions({ toolbar, inline }),
        getPasteExtension({ toolbar, inline }),
        getPlaceholderExtension(placeholder),
        getTrailingNodeExtension({ inline }),
        Html,
        Selected,
    ];
    return extensions
        .flat()
        .filter(extension => !!extension);
}

export * from './upload';
