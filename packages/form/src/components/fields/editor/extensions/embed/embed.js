import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import EmbedNode from "./EmbedNode";
import {
    serializeAttributeValue,
    parseAttributeValue,
    kebabCase,
    additionalDataAttributeName,
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
                parseHTML: element => parseAttributeValue(element.getAttribute(additionalDataAttributeName)),
                renderHTML: attributes => {
                    if(!attributes.additionalData) {
                        return null;
                    }
                    return {
                        [additionalDataAttributeName]: serializeAttributeValue(
                            Object.fromEntries(
                                Object.entries(attributes.additionalData)
                                    .filter(([key]) => !this.options.attributes.includes(key))
                            )
                        ),
                    }
                },
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
