import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import EmbedNode from "./EmbedNode.vue";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { EmbedData } from "@/types";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { Form } from "@/form/Form";
import { Plugin } from "@tiptap/pm/state";
import { Fragment, Slice } from "@tiptap/pm/model";
import { getAllNodesAfterUpdate } from "@/form/components/fields/editor/utils/tiptap/getAllNodesAfterUpdate";

export type EmbedNodeAttributes = {
    'data-key': string,
    'data-value': EmbedData['value'],
}

export type EmbedOptions = {
    embed: EmbedData,
    embedManager: ContentEmbedManager<Form>,
    locale?: string,
}

export const Embed: WithRequiredOptions<Node<EmbedOptions>> = Node.create<EmbedOptions>({
    name: 'embed',

    group: 'embed',

    atom: true,

    isolating: true,

    priority: 150,

    addAttributes(): ExtensionAttributesSpec<EmbedNodeAttributes> {
        return {
            'data-key': {},
            'data-value': {
                parseHTML(element) {
                    return element.hasAttribute('data-value')
                        ? JSON.parse(element.getAttribute('data-value'))
                        : null;
                },
                renderHTML(attributes: EmbedNodeAttributes) {
                    return {
                        'data-value': attributes['data-value']
                            ? JSON.stringify(attributes['data-value'])
                            : null
                    };
                },
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

    renderHTML({ HTMLAttributes }) {
        return [this.options.embed.tag, HTMLAttributes];
    },

    addProseMirrorPlugins() {
        const { name, options } = this;
        return [
            new Plugin({
                props: {
                    transformCopied(slice: Slice) {
                        return new Slice(
                            Fragment.fromArray(
                                slice.content.content.map(node => {
                                    if(node.type.name === name) {
                                        return node.type.create({
                                            ...node.attrs,
                                            'data-value': options.embedManager.getEmbed(node.attrs['data-key'], options.embed),
                                        })
                                    }
                                    return node;
                                })
                            ),
                            slice.openStart,
                            slice.openEnd
                        );
                    },
                }
            })
        ]
    },

    onUpdate({ transaction, appendedTransactions }) {
        this.options.embedManager.syncEmbeds(
            getAllNodesAfterUpdate(this.name, transaction, appendedTransactions)
                .map(node => node.attrs['data-key']),
            this.options.embed,
            this.options.locale
        )
    },

    addCommands() {
        return {
            insertEmbed: ({ id, embed }) => ({ commands, tr }) => {
                return commands
                    .insertContentAt(tr.selection.to, {
                        type: `embed:${embed.key}`,
                        attrs: {
                            'data-key': id,
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
            insertEmbed: (params: { id: string, embed: EmbedData }) => ReturnType
        }
    }
}
