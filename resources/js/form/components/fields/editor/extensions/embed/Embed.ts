import { AnyCommands, Node, type Range } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import { serializeAttributeValue, parseAttributeValue, serializeUploadAttributeValue } from "@/embeds/utils/attributes";
import EmbedNode from "./EmbedNode.vue";
import { hyphenate } from "@/utils";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { EmbedData, FormData, FormUploadFieldValueData } from "@/types";
import { EmbedManager } from "@/embeds/EmbedManager";
import { UploadOptions } from "@/form/components/fields/editor/extensions/upload/Upload";

export type EmbedNodeAttributes = {
    embedAttributes: EmbedData['value'],
    additionalData: { [key: string]: any },
    isNew: boolean,
    'data-unique-id': string,
}

export type EmbedOptions = {
    embed: EmbedData | null,
    embedManager: EmbedManager,
}

export const Embed: WithRequiredOptions<Node<EmbedOptions>> = Node.create<EmbedOptions>({
    name: 'embed',

    group: 'block',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<EmbedNodeAttributes> {
        const embed: EmbedData = this.options.embed;
        return {
            embedAttributes: {
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
                renderHTML: (attributes: EmbedNodeAttributes) => {
                    return Object.fromEntries(
                        embed.attributes
                            ?.filter(attributeName => attributes.embedAttributes[attributeName] != null)
                            .map((attributeName) => {
                                const value = embed.fields[attributeName].type === 'upload'
                                    ? serializeUploadAttributeValue(attributes.embedAttributes[attributeName] as FormUploadFieldValueData)
                                    : serializeAttributeValue(attributes.embedAttributes[attributeName]);

                                return [hyphenate(attributeName), value];
                            }, {})
                        ?? []
                    )
                },
            },
            'data-unique-id': {
                default: null,
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
                tag: this.options.embed.tag,
            },
        ]
    },

    renderHTML({ node, HTMLAttributes }) {
        const element = document.createElement(this.options.embed.tag);

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
            insertEmbed: (embed: EmbedData) => ({ commands, tr }) => {
                return commands.insertContentAt(tr.selection.to, {
                    type: `embed:${embed.key}`,
                    attrs: {
                        isNew: true,
                        'data-unique-id': this.options.embedManager.newId(embed)
                    },
                });
            },
        }
    },

    addNodeView() {
        return VueNodeViewRenderer(EmbedNode);
    },
});

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        embed: {
            insertEmbed: (embed: EmbedData) => ReturnType
        }
    }
}
