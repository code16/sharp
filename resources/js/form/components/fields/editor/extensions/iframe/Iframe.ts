import { Node } from '@tiptap/core'
import { PasteRule } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import IframeNode from "./IframeNode.vue";
import { ExtensionAttributesSpec } from "@/form/components/fields/editor/types";

export type IframeAttributes = {
    src: string,
    frameborder: string,
    width: string,
    height: string,
    allow: string,
    allowfullscreen: string,
    isNew: boolean,
}

export const Iframe = Node.create({
    name: 'iframe',

    group: 'block',

    atom: true,

    addOptions: () => ({
        HTMLAttributes: {
            class: 'iframe-wrapper',
        },
    }),

    addAttributes(): ExtensionAttributesSpec<IframeAttributes> {
        return {
            src: {
                default: null,
            },
            frameborder: {
                default: '0',
            },
            width: {
                default: null
            },
            height: {
                default: null
            },
            allow: {
                default: null,
            },
            allowfullscreen: {
                default: null,
            },
            isNew: {
                default: false,
                rendered: false,
            },
        }
    },

    parseHTML() {
        return [{
            tag: 'iframe',
        }]
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', this.options.HTMLAttributes, ['iframe', HTMLAttributes]]
    },

    addPasteRules() {
        return [
            new PasteRule({
                find: /(?:^|\s)(<iframe(.+)<\/iframe>).*/g,
                handler: ({ state, range, match }) => {
                    const html = match[1];
                    setTimeout(() => {
                        this.editor.commands.insertContentAt(range, html);
                    });
                }
            }),
        ]
    },

    addCommands() {
        return {
            insertIframe: () => ({ commands, tr }) => {
                return commands.insertContentAt(tr.selection.to, {
                    type: this.name,
                    attrs: {
                        isNew: true,
                    },
                });
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(IframeNode);
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        iframe: {
            insertIframe: () => ReturnType
        }
    }
}
