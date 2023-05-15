import { Plugin } from "@tiptap/pm/state";
import { Decoration, DecorationSet } from "@tiptap/pm/view";
import { Extension } from "@tiptap/core";

export const Selected = Extension.create({
    name: 'selected',
    addProseMirrorPlugins() {
        return [
            new Plugin({
                props: {
                    decorations(state) {
                        const selection = state.selection;
                        const decorations = [];

                        state.doc.nodesBetween(selection.from, selection.to, (node, position) => {
                            if (node.isBlock) {
                                decorations.push(Decoration.node(position, position + node.nodeSize, {class: 'selected'}));
                            }
                        });

                        return DecorationSet.create(state.doc, decorations);
                    }
                }
            })
        ]
    }
})
