import StarterKit from "@tiptap/starter-kit";
import Table from "@tiptap/extension-table";
import TableRow from "@tiptap/extension-table-row";
import TableHeader from "@tiptap/extension-table-header";
import TableCell from "@tiptap/extension-table-cell";
import Image from "@tiptap/extension-image";
import HorizontalRule from "@tiptap/extension-horizontal-rule";
import Link from "@tiptap/extension-link";
import Placeholder from '@tiptap/extension-placeholder';
import Heading from "@tiptap/extension-heading";
import { Upload } from "./extensions/upload/upload";
import { TrailingNode } from "./extensions/trailing-node";
import { filesEquals } from "sharp-files";
import { getAllowedHeadingLevels, toolbarHasButton } from "./util";


export const defaultEditorOptions = {
    injectCSS: false,
}

function getHeadingExtension(toolbar) {
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
    if(toolbarHasButton(toolbar, 'image')) {
        return Image.configure({
            HTMLAttributes: {
                class: 'editor__image',
            },
        });
    }
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

export function getDefaultExtensions({ placeholder, toolbar } = {}) {
    const bulletList = toolbarHasButton(toolbar, 'bullet-list');
    const orderedList = toolbarHasButton(toolbar, 'ordered-list');
    const extensions = [
        StarterKit.configure({
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
        }),
        getHeadingExtension(toolbar),
        getLinkExtension(toolbar),
        getImageExtension(toolbar),
        getHorizontalRuleExtension(toolbar),
        getTableExtensions(toolbar),
        getPlaceholderExtension(placeholder),
        TrailingNode,
    ];
    return extensions
        .flat()
        .filter(extension => !!extension);
}

export function getUploadExtension({ fieldProps }) {
    return Upload.configure({
        fieldProps: {
            ...fieldProps,
            uniqueIdentifier: this.uniqueIdentifier,
            fieldConfigIdentifier: this.fieldConfigIdentifier,
        },
        getFile: attrs => {
            return this.value.files?.find(file => filesEquals(attrs, file));
        },
        onSuccess: (value) => {
            this.$emit('input', {
                ...this.value,
                files: [...(this.value?.files ?? []), value],
            });
        },
        onRemove: (value) => {
            this.$emit('input', {
                ...this.value,
                files: this.value.files?.filter(file => !filesEquals(file, value)),
            });
        },
        onUpdate: (value) => {
            this.$emit('input', {
                ...this.value,
                files: this.value.files?.map(file => filesEquals(file, value) ? value : file),
            });
        },
    });
}
