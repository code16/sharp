import { Upload } from "./extensions/upload/upload";
import { filesEquals } from "../../../util/upload";
import StarterKit from "@tiptap/starter-kit";
import Table from "@tiptap/extension-table";
import TableRow from "@tiptap/extension-table-row";
import TableHeader from "@tiptap/extension-table-header";
import TableCell from "@tiptap/extension-table-cell";
import Image from "@tiptap/extension-image";
import HorizontalRule from "@tiptap/extension-horizontal-rule";
import Link from "@tiptap/extension-link";
import Placeholder from '@tiptap/extension-placeholder';
import { TrailingNode } from "./extensions/trailing-node";

export function getDefaultExtensions({ placeholder } = {}) {
    const extensions = [
        StarterKit.configure({
            horizontalRule: false,
        }),
        Table,
        TableRow,
        TableHeader,
        TableCell,
        Image,
        HorizontalRule.extend({
            selectable: false,
        }),
        Link.configure({
            openOnClick: false,
        }),
        TrailingNode,
    ];

    if(placeholder) {
        extensions.push(Placeholder.configure({
            placeholder,
        }));
    }

    return extensions;
}

export function getUploadExtension({ fieldProps }) {
    return Upload.configure({
        fieldProps: {
            ...fieldProps,
            uniqueIdentifier: this.uniqueIdentifier,
            fieldConfigIdentifier: this.fieldConfigIdentifier,
        },
        findFile: attrs => {
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
