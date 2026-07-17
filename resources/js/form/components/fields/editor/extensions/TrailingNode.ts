import { Extension } from '@tiptap/core'
import type { Node, NodeType } from '@tiptap/pm/model'
import { EditorState, Plugin, PluginKey } from '@tiptap/pm/state'
import { Footnotes } from "tiptap-footnotes";

export const skipTrailingNodeMeta = 'skipTrailingNode'

function nodeEqualsType({
    types,
    node,
}: {
    types: NodeType | NodeType[]
    node: Node | null | undefined
}) {
    return (node && Array.isArray(types) && types.includes(node.type)) || node?.type === types
}

// New function that handles the footnote at the end
function getLastNode(doc: Node) {
    const { lastChild } = doc
    if (lastChild?.type.name === Footnotes.name) {
        return {
            node: doc.content.child(doc.childCount - 2),
            pos: doc.content.size - lastChild.nodeSize,
        }
    }
    return {
        node: lastChild,
        pos: doc.content.size,
    }
}

/**
 * Extension based on:
 * - https://github.com/ueberdosis/tiptap/blob/v1/packages/tiptap-extensions/src/extensions/TrailingNode.js
 * - https://github.com/remirror/remirror/blob/e0f1bec4a1e8073ce8f5500d62193e52321155b9/packages/prosemirror-trailing-node/src/trailing-node-plugin.ts
 */

export interface TrailingNodeOptions {
    /**
     * The node type that should be inserted at the end of the document.
     * @note the node will always be added to the `notAfter` lists to
     * prevent an infinite loop.
     * @default undefined
     */
    node?: string
    /**
     * The node types after which the trailing node should not be inserted.
     * @default ['paragraph']
     */
    notAfter?: string | string[]
}

/**
 * This extension allows you to add an extra node at the end of the document.
 * @see https://www.tiptap.dev/api/extensions/trailing-node
 */
export const TrailingNode = Extension.create<TrailingNodeOptions>({
    name: 'trailingNode',

    addOptions() {
        return {
            node: undefined,
            notAfter: [],
        }
    },

    addProseMirrorPlugins() {
        const plugin = new PluginKey(this.name)
        const defaultNode =
            this.options.node ||
            this.editor.schema.topNodeType.contentMatch.defaultType?.name ||
            'paragraph'

        const disabledNodes = Object.entries(this.editor.schema.nodes)
            .map(([, value]) => value)
            .filter(node => (this.options.notAfter || []).concat(defaultNode).includes(node.name))

        return [
            new Plugin({
                key: plugin,
                appendTransaction: (transactions, __, state) => {
                    const { doc, tr, schema } = state
                    const shouldInsertNodeAtEnd = plugin.getState(state)
                    const { pos: endPosition } = getLastNode(doc)
                    const type = schema.nodes[defaultNode]

                    if (transactions.some(transaction => transaction.getMeta(skipTrailingNodeMeta))) {
                        return
                    }

                    if (!shouldInsertNodeAtEnd) {
                        return
                    }

                    return tr.insert(endPosition, type.create())
                },
                state: {
                    init: (_, state) => {
                        const { node: lastNode } = getLastNode(state.tr.doc)
                        console.log('lastNode', lastNode)

                        return !nodeEqualsType({ node: lastNode, types: disabledNodes })
                    },
                    apply: (tr, value) => {
                        if (!tr.docChanged) {
                            return value
                        }

                        // Ignore transactions from UniqueID extension to prevent infinite loops
                        // when UniqueID adds IDs to newly inserted trailing nodes
                        if (tr.getMeta('__uniqueIDTransaction')) {
                            return value
                        }

                        const { node: lastNode } = getLastNode(tr.doc)

                        return !nodeEqualsType({ node: lastNode, types: disabledNodes })
                    },
                },
            }),
        ]
    },
})
