import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import EmbedNode from "./EmbedNode";

export const Embed = Node.create({
    name: 'embed',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addOptions: () => ({
        label: null,
        tag: null,
        attributes: [],
        template: null,
    }),

    addAttributes() {
        const attributes =
            this.options.attributes
                .reduce((res, attributeName) => ({
                    ...res,
                    [attributeName]: {
                        default: null,
                    }
                }), {})
        return {
            ...attributes,
            additionalData: {
                default: null,
                renderHTML: () => null,
            },
            isNew: {
                default: false,
                renderHTML: () => null,
            },
        }
    },

    parseHTML() {
        return [
            {
                tag: this.options.tag,
            },
        ]
    },

    renderHTML({ node, HTMLAttributes }) {
        return [this.options.tag, HTMLAttributes];
    },

    addCommands() {
        return {
            insertEmbed: ({ embedKey }) => ({ commands, tr }) => {
                return commands
                    .insertContentAt(tr.selection, {
                        type: `embed:${embedKey}`,
                        attrs: {
                            isNew: true,
                        },
                    });
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(EmbedNode);
    },
});
