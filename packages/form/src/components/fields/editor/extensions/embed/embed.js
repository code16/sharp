import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-2";
import { serializeAttributeValue, parseAttributeValue } from "sharp-embeds";
import EmbedNode from "./EmbedNode.vue";
import { hyphenate } from "./util";

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
                parseHTML: element => {
                    const attributes = this.options.attributes
                        .reduce((res, attributeName) => ({
                            ...res,
                            [attributeName]: parseAttributeValue(element.getAttribute(hyphenate(attributeName))),
                        }), {});

                    if(this.options.attributes.includes('slot')) {
                        attributes.slot = element.innerHTML;
                    }

                    return attributes;
                },
                renderHTML: attributes => this.options.attributes
                    .filter(attributeName => attributes.attributes[attributeName] != null)
                    .reduce((res, attributeName) => ({
                        ...res,
                        [hyphenate(attributeName)]: serializeAttributeValue(attributes.attributes[attributeName]),
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
        const element = document.createElement(this.options.tag);

        Object.entries(HTMLAttributes)
            .filter(([name]) => name !== 'slot')
            .forEach(([name, value]) => {
                element.setAttribute(name, value);
            });

        if(HTMLAttributes.slot) {
            element.innerHTML = HTMLAttributes.slot;
        }

        return element;
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
