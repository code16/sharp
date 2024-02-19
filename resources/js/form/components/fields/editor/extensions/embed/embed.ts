import { AnyCommands, Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import { serializeAttributeValue, parseAttributeValue, serializeUploadAttributeValue } from "@/embeds/utils/attributes";
import EmbedNode from "./EmbedNode.vue";
import { hyphenate } from "@/utils";
import { EmbedOptions } from "./index";

export const Embed = Node.create<EmbedOptions>({
    name: 'embed',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes() {
        const embed = this.options.embed;
        return {
            attributes: {
                default: {},
                parseHTML: (element) => {
                    const attributes = Object.fromEntries(
                        embed.attributes.map(attributeName =>
                            [attributeName, parseAttributeValue(element.getAttribute(hyphenate(attributeName)))]
                        )
                    );

                    if(embed.attributes?.includes('slot')) {
                        attributes.slot = element.innerHTML;
                    }

                    return attributes;
                },
                renderHTML: (attributes) => {
                    return Object.fromEntries(
                        embed.attributes
                            ?.filter(attributeName => attributes.attributes[attributeName] != null)
                            .map((attributeName) => {
                                const value = embed.fields[attributeName].type === 'upload'
                                    ? serializeUploadAttributeValue(attributes.attributes[attributeName])
                                    : serializeAttributeValue(attributes.attributes[attributeName]);

                                return [hyphenate(attributeName), value];
                            }, {})
                        ?? []
                    )
                },
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

    addCommands(): AnyCommands {
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
