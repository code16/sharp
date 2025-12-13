import { CodeBlock as BaseCodeBlock } from '@tiptap/extension-code-block';
import { Plugin } from "@tiptap/pm/state";
import { PluginKey } from "prosemirror-state";
import { Decoration, DecorationSet } from "prosemirror-view";

export const CodeBlock = BaseCodeBlock.extend({
    addProseMirrorPlugins() {
        return [
            ...this.parent(),
            new Plugin({
                key: new PluginKey('codeBlockLanguageDecoration'),
                props: {
                    decorations(state) {
                        const decos: Decoration[] = [];

                        state.doc.descendants((node, pos) => {
                            if (node.type.name === CodeBlock.name && node.attrs.language) {
                                decos.push(
                                    Decoration.node(pos, pos + node.nodeSize, {
                                        'data-language': node.attrs.language
                                    })
                                );
                            }
                        });

                        return DecorationSet.create(state.doc, decos);
                    }
                },
            })
        ]
    }
})
