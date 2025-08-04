import { Node } from "@tiptap/core";
import { VueNodeViewRenderer } from "@tiptap/vue-3";
import EmbedNode from "./EmbedNode.vue";
import { ExtensionAttributesSpec, WithRequiredOptions } from "@/form/components/fields/editor/types";
import { EmbedData } from "@/types";
import { ContentEmbedManager } from "@/content/ContentEmbedManager";
import { Form } from "@/form/Form";

export type EmbedNodeAttributes = {
    'data-key': string,
}

export type EmbedOptions = {
    embed: EmbedData,
    embedManager: ContentEmbedManager<Form>,
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
