import Heading from "@tiptap/extension-heading";
import Link from "@tiptap/extension-link";
import Image from "@tiptap/extension-image";
import HorizontalRule from "@tiptap/extension-horizontal-rule";
import Table from "@tiptap/extension-table";
import TableRow from "@tiptap/extension-table-row";
import TableHeader from "@tiptap/extension-table-header";
import TableCell from "@tiptap/extension-table-cell";
import Placeholder from "@tiptap/extension-placeholder";
import StarterKit from "@tiptap/starter-kit";
import { TrailingNode } from "./trailing-node";
import { getAllowedHeadingLevels, toolbarHasButton } from "../util";
import { isInTable} from 'prosemirror-tables';
import { mergeAttributes } from "@tiptap/core";

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
        let selectedNode = null;
        return [
            Table
                .configure({
                    // allowTableNodeSelection: true,
                })
                .extend({
                    addAttributes() {
                        return {
                            active: {
                                default: false,
                            }
                        }
                    },
                    onTransaction({ transaction }) {
                        // let $head = transaction.selection.$head
                        // let found = false;
                        // let depth = $head.depth;
                        // const getPos = () => {
                        //     let resolved = 0;
                        //     this.editor.state.doc.descendants((node, pos) => {
                        //         if(node === selectedNode) {
                        //             resolved = pos;
                        //         }
                        //     });
                        //     return resolved;
                        // }
                        // for (; depth > 0; depth--) {
                        //     const node = $head.node(depth);
                        //     if (node.type.spec.tableRole === "table") {
                        //         selectedNode = node;
                        //         found = true;
                        //         setTimeout(() => {
                        //             this.editor.state.tr.setNodeMarkup(getPos(), undefined, {
                        //                 ...node.attrs,
                        //                 active: true,
                        //             });
                        //         })
                        //     }
                        // }
                        //
                        // if(!found && selectedNode) {
                        //     this.editor.state.tr.setNodeMarkup(getPos(), undefined, {
                        //         ...selectedNode.attrs,
                        //         active: false,
                        //     });
                        // }
                    },
                }),
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

export * from './upload';
