import { Extension } from "@tiptap/core";
import { Decoration, DecorationSet } from "@tiptap/pm/view";
import { Plugin } from "@tiptap/pm/state";
import { cn } from "@/utils/cn";

export const DecorateHiddenCharacters = Extension.create({
    name: 'decorateHiddenCharacters',

    addOptions() {
        return {
            class: '',
        }
    },


    addProseMirrorPlugins() {
        const buildDecorations = (doc) => {
            const decorations = []


            doc.descendants((node, pos) => {
                if (!node.isText) return true


                const text = node.text
                if (!text) return true

                let idx = text.indexOf('\u00A0')
                while (idx !== -1) {
                    const from = pos + idx
                    const to = from + 1
                    const deco = Decoration.inline(from, to, {
                        'data-key': 'nbsp',
                        class: this.options.class,
                    })
                    decorations.push(deco)
                    idx = text.indexOf('\u00A0', idx + 1)
                }


                return true
            })


            return DecorationSet.create(doc, decorations)
        }


        return [
            new Plugin({
                state: {
                    init(_, { doc }) {
                        return buildDecorations(doc)
                    },
                    apply(tr, old) {
                        if (!tr.docChanged) return old
                        return buildDecorations(tr.doc)
                    },
                },
                props: {
                    decorations(state) {
                        return this.getState(state)
                    },
                },
            }),
        ]
    },
})
