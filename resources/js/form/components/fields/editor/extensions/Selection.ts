import { Plugin, Transaction } from "@tiptap/pm/state";
import { Decoration, DecorationSet } from "@tiptap/pm/view";
import { Extension } from "@tiptap/core";

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        selected: {
            toggleSelectionHighlight: (show: boolean) => ReturnType,
        },
    }
}

type SelectedPluginState = {
    highlight: boolean,
}

export const Selection = Extension.create({
    name: 'selected',
    addCommands() {
        return {
            toggleSelectionHighlight: (show: boolean) => ({ tr, state }) => {
                tr.setMeta('toggleSelectionHighlight', show);
                return !state.selection.empty;
            }
        }
    },
    addProseMirrorPlugins() {
        const plugin = new Plugin<SelectedPluginState>({
            state: {
                init: () => ({ highlight: false }),
                apply: (tr: Transaction, state) => {
                    return {
                        highlight: tr.getMeta('toggleSelectionHighlight') ?? state.highlight,
                    };
                }
            },
            props: {
                decorations(state) {
                    const pluginState = plugin.getState(state);
                    const selection = state.selection;
                    const decorations = [];

                    if(!selection.empty && pluginState.highlight) {
                        decorations.push(Decoration.inline(selection.from, selection.to, { class: 'selection-highlight' }));
                    }

                    state.doc.nodesBetween(selection.from, selection.to, (node, position) => {
                        if (node.isBlock) {
                            decorations.push(Decoration.node(position, position + node.nodeSize, { 'data-textselected': 'true' }));
                        }
                    });

                    return DecorationSet.create(state.doc, decorations);
                }
            }
        });

        return [
            plugin,
        ]
    }
});
