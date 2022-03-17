import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import EmbedNode from "./EmbedNode";
import {
    serializeAttributeValue,
    parseAttributeValue,
    kebabCase,
} from "./util";

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
        return {
            attributes: {
                default: {},
                parseHTML: element => this.options.attributes
                    .reduce((res, attributeName) => ({
                        ...res,
                        [attributeName]: parseAttributeValue(element.getAttribute(kebabCase(attributeName))),
                    }), {}),
                renderHTML: attributes => this.options.attributes
                    .reduce((res, attributeName) => ({
                        ...res,
                        [kebabCase(attributeName)]: serializeAttributeValue(attributes.attributes[attributeName]),
                    }), {}),
            },
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
                    .insertContentAt(tr.selection.to, {
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
