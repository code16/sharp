import { Node } from '@tiptap/core'
import { PasteRule } from "@tiptap/core";

export const Iframe =  Node.create({
    name: 'iframe',

    group: 'block',

    atom: true,

    defaultOptions: {
        allowFullscreen: false,
        HTMLAttributes: {
            class: 'iframe-wrapper',
        },
    },

    addAttributes() {
        return {
            src: {
                default: null,
            },
            frameborder: {
                default: 0,
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
                default: this.options.allowFullscreen,
                parseHTML: () => this.options.allowFullscreen,
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
                find: /(?:^|\s)<iframe(.+)<\/iframe>/g,
                handler: ({ state, range, match }) => {
                    const html = match[0];
                    setTimeout(() => {
                        this.editor.commands.insertContentAt(range, html);
                    });
                }
            }),
        ]
    },

    addCommands() {
        return {
            insertIframe: (html) => ({ commands }) => {
                const match = html?.match(/<iframe(.+)<\/iframe>/);
                if(match) {
                    commands.insertContent(match[0]);
                }
                return true
            },
        }
    },
});
