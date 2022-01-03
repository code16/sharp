import { getExtensionField, getSchema } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import { Heading } from "@tiptap/extension-heading";
import { Link } from "@tiptap/extension-link";
import { Image } from "@tiptap/extension-image";
import { HorizontalRule } from "@tiptap/extension-horizontal-rule";
import { Placeholder } from "@tiptap/extension-placeholder";
import { Table } from "@tiptap/extension-table";
import { TableRow } from "@tiptap/extension-table-row";
import { TableHeader } from "@tiptap/extension-table-header";
import { TableCell } from "@tiptap/extension-table-cell";
import { Highlight } from "@tiptap/extension-highlight";
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

function getPasteExtension(toolbar) {
    const extensions = getToolbarExtensions(toolbar);
    const schema = getSchema(extensions);
    return Paste.configure({
        schema,
    });
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
        hardBreak: true,
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

function getToolbarExtensions(toolbar) {
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
    ];
    return extensions
        .flat()
        .filter(extension => !!extension);
}

export function getDefaultExtensions({ placeholder, toolbar } = {}) {
    const extensions = [
        getToolbarExtensions(toolbar),
        getPlaceholderExtension(placeholder),
        getPasteExtension(toolbar),
        Html,
        TrailingNode,
        Selected,
    ];
    return extensions
        .flat()
        .filter(extension => !!extension);
}

export * from './upload';
