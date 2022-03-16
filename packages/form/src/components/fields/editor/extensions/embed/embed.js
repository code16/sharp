import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import EmbedNode from "./EmbedNode";

function parseAttributeValue(value) {
    try {
        return JSON.parse(value);
    } catch {
        return value;
    }
}

function serializeAttributeValue(value) {
    if(value && typeof value === 'object') {
        return JSON.stringify(value);
    }

    return value;
}


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
                        parseHTML: (element) => parseAttributeValue(element.getAttribute(attributeName)),
                        renderHTML: (attributes) => ({
                            [attributeName]: serializeAttributeValue(attributes[attributeName]),
                        }),
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
